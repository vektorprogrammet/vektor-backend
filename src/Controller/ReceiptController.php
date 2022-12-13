<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Entity\User;
use App\Event\ReceiptEvent;
use App\Form\Type\ReceiptType;
use App\Role\Roles;
use App\Service\FileUploader;
use App\Service\RoleManager;
use App\Service\Sorter;
use App\Utils\ReceiptStatistics;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReceiptController extends BaseController
{
    private Sorter $sorter;
    private FileUploader $fileUploader;
    private EventDispatcherInterface $eventDispatcher;
    private RoleManager $roleManager;

    public function __construct(
        Sorter $sorter,
        FileUploader $fileUploader,
        EventDispatcherInterface $eventDispatcher,
        RoleManager $roleManager
    ) {
        $this->sorter = $sorter;
        $this->fileUploader = $fileUploader;
        $this->eventDispatcher = $eventDispatcher;
        $this->roleManager = $roleManager;
    }

    public function show(): Response
    {
        $usersWithReceipts = $this->getDoctrine()->getRepository(User::class)->findAllUsersWithReceipts();
        $refundedReceipts = $this->getDoctrine()->getRepository(Receipt::class)->findByStatus(Receipt::STATUS_REFUNDED);
        $pendingReceipts = $this->getDoctrine()->getRepository(Receipt::class)->findByStatus(Receipt::STATUS_PENDING);
        $rejectedReceipts = $this->getDoctrine()->getRepository(Receipt::class)->findByStatus(Receipt::STATUS_REJECTED);

        $refundedReceiptStatistics = new ReceiptStatistics($refundedReceipts);
        $totalPayoutThisYear = $refundedReceiptStatistics->totalPayoutIn((new \DateTime())->format('Y'));
        $avgRefundTimeInHours = $refundedReceiptStatistics->averageRefundTimeInHours();

        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);
        $rejectedReceiptStatistics = new ReceiptStatistics($rejectedReceipts);

        $sorter = $this->sorter;

        $sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $sorter->sortUsersByReceiptStatus($usersWithReceipts);

        return $this->render('receipt_admin/show_receipts.html.twig', [
            'users_with_receipts' => $usersWithReceipts,
            'current_user' => $this->getUser(),
            'total_payout' => $totalPayoutThisYear,
            'avg_refund_time_in_hours' => $avgRefundTimeInHours,
            'pending_statistics' => $pendingReceiptStatistics,
            'rejected_statistics' => $rejectedReceiptStatistics,
            'refunded_statistics' => $refundedReceiptStatistics,
        ]);
    }

    public function showIndividual(User $user): Response
    {
        $receipts = $this->getDoctrine()->getRepository(Receipt::class)->findByUser($user);

        $sorter = $this->sorter;
        $sorter->sortReceiptsBySubmitTime($receipts);
        $sorter->sortReceiptsByStatus($receipts);

        return $this->render('receipt_admin/show_individual_receipts.html.twig', [
            'user' => $user,
            'receipts' => $receipts,
        ]);
    }

    public function create(Request $request)
    {
        $receipt = new Receipt();
        $receipt->setUser($this->getUser());

        $receipts = $this->getDoctrine()->getRepository(Receipt::class)->findByUser($this->getUser());

        $sorter = $this->sorter;
        $sorter->sortReceiptsBySubmitTime($receipts);
        $sorter->sortReceiptsByStatus($receipts);

        $form = $this->createForm(ReceiptType::class, $receipt);

        $form->handleRequest($request);

        // User has posted a receipt
        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = $request->files->get('receipt', ['picture_path']) !== null;
            if ($isImageUpload) {
                $path = $this->fileUploader->uploadReceipt($request);
                $receipt->setPicturePath($path);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::CREATED);

            return $this->redirectToRoute('receipt_create');
        }
        // Else: User is viewing receipt page (no receipt exist: path=null)

        $receipt->setPicturePath('');

        if ($form->isSubmitted() && !$form->isValid() && $receipt->getPicturePath() === '') {
            $this->addFlash('warning', 'Bildefilen er for stor. Maks størrelse er 2 MiB.');
        }

        return $this->render('receipt/my_receipts.html.twig', [
            'form' => $form->createView(),
            'receipt' => $receipt,
            'receipts' => $receipts,
        ]);
    }

    public function edit(Request $request, Receipt $receipt)
    {
        $user = $this->getUser();

        $userCanEditReceipt = $user === $receipt->getUser() && $receipt->getStatus() === Receipt::STATUS_PENDING;

        if (!$userCanEditReceipt) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ReceiptType::class, $receipt, [
            'picture_required' => false,
        ]);
        $oldPicturePath = $receipt->getPicturePath();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = array_values($request->files->get('receipt', ['picture_path']))[0] !== null;

            if ($isImageUpload) {
                // Delete the old image file
                $this->fileUploader->deleteReceipt($oldPicturePath);

                $path = $this->fileUploader->uploadReceipt($request);
                $receipt->setPicturePath($path);
            } else {
                $receipt->setPicturePath($oldPicturePath);
            } // If a new image hasn't been uploaded

            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::EDITED);

            return $this->redirectToRoute('receipt_create');
        }

        return $this->render('receipt/edit_receipt.html.twig', [
            'form' => $form->createView(),
            'receipt' => $receipt,
            'parent_template' => 'base.html.twig',
        ]);
    }

    public function editStatus(Request $request, Receipt $receipt): RedirectResponse
    {
        $status = $request->get('status');
        if ($status !== Receipt::STATUS_PENDING &&
            $status !== Receipt::STATUS_REFUNDED &&
            $status !== Receipt::STATUS_REJECTED) {
            throw new BadRequestHttpException('Invalid status');
        }

        if ($status === $receipt->getStatus()) {
            return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
        }

        $receipt->setStatus($status);
        if ($status === Receipt::STATUS_REFUNDED && !$receipt->getRefundDate()) {
            $receipt->setRefundDate(new \DateTime());
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        if ($status === Receipt::STATUS_REFUNDED) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::REFUNDED);
        } elseif ($status === Receipt::STATUS_REJECTED) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::REJECTED);
        } elseif ($status === Receipt::STATUS_PENDING) {
            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::PENDING);
        }

        return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
    }

    public function adminEdit(Request $request, Receipt $receipt)
    {
        $form = $this->createForm(ReceiptType::class, $receipt, [
            'picture_required' => false,
        ]);
        $oldPicturePath = $receipt->getPicturePath();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = array_values($request->files->get('receipt', ['picture_path']))[0] !== null;

            if ($isImageUpload) {
                // Delete the old image file
                $this->fileUploader->deleteReceipt($oldPicturePath);

                $path = $this->fileUploader->uploadReceipt($request);
                $receipt->setPicturePath($path);
            } else {
                $receipt->setPicturePath($oldPicturePath);
            } // If a new image hasn't been uploaded

            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::EDITED);

            return $this->redirectToRoute('receipts_show_individual', ['user' => $receipt->getUser()->getId()]);
        }

        return $this->render('receipt/edit_receipt.html.twig', [
            'form' => $form->createView(),
            'receipt' => $receipt,
            'parent_template' => 'adminBase.html.twig',
        ]);
    }

    public function delete(Request $request, Receipt $receipt): RedirectResponse
    {
        $user = $this->getUser();
        $isTeamLeader = $this->roleManager->userIsGranted($user, Roles::TEAM_LEADER);

        $userCanDeleteReceipt = $isTeamLeader || ($user === $receipt->getUser() && $receipt->getStatus() === Receipt::STATUS_PENDING);

        if (!$userCanDeleteReceipt) {
            throw new AccessDeniedException();
        }

        // Delete the image file
        $this->fileUploader->deleteReceipt($receipt->getPicturePath());

        $em = $this->getDoctrine()->getManager();
        $em->remove($receipt);
        $em->flush();

        $this->eventDispatcher->dispatch(new ReceiptEvent($receipt), ReceiptEvent::DELETED);

        return $this->redirect($request->headers->get('referer'));
    }
}
