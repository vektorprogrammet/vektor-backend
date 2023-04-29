<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\CertificateRequest;
use App\Entity\Signature;
use App\Form\Type\CreateSignatureType;
use App\Service\DepartmentSemesterService;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends AbstractController
{
    public function __construct(
        private readonly FileUploader $fileUploader,
        private readonly ManagerRegistry $doctrine,
        private readonly DepartmentSemesterService $departmentSemesterService,
    ) {
    }

    public function show(Request $request): RedirectResponse|Response
    {
        $user = $this->getUser();
        $department = $this->departmentSemesterService->getDepartmentOrThrow404($request, $user);
        $semester = $this->departmentSemesterService->getSemesterOrThrow404($request);
        $em = $this->doctrine->getManager();

        $assistants = $em
            ->getRepository(AssistantHistory::class)
            ->findByDepartmentAndSemester($department, $semester);

        $signature = $this->doctrine
            ->getRepository(Signature::class)
            ->findByUser($this->getUser());

        $oldPath = '';
        if ($signature === null) {
            $signature = new Signature();
        } else {
            $oldPath = $signature->getSignaturePath();
        }

        $form = $this->createForm(CreateSignatureType::class, $signature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = $request->files->get('create_signature')['signature_path'] !== null;

            if ($isImageUpload) {
                $signaturePath = $this->fileUploader->uploadSignature($request);
                $this->fileUploader->deleteSignature($oldPath);

                $signature->setSignaturePath($signaturePath);
            } else {
                $signature->setSignaturePath($oldPath);
            }

            $signature->setUser($this->getUser());
            $manager = $this->doctrine->getManager();
            $manager->persist($signature);
            $manager->flush();

            $this->addFlash('success', 'Signatur og evt. kommentar ble lagret');

            return $this->redirect($request->headers->get('referer'));
        }

        // Finds all the certificate requests
        $certificateRequests = $this->doctrine
            ->getRepository(CertificateRequest::class)
            ->findAll();

        return $this->render('certificate/index.html.twig', [
            'certificateRequests' => $certificateRequests,
            'form' => $form->createView(),
            'signature' => $signature,
            'assistants' => $assistants,
            'department' => $department,
            'currentSemester' => $semester,
        ]);
    }
}
