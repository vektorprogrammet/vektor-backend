<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\CertificateRequest;
use App\Entity\Signature;
use App\Form\Type\CreateSignatureType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends BaseController
{
    private FileUploader $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    /**
     * @return RedirectResponse|Response
     */
    public function show(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $em = $this->getDoctrine()->getManager();

        $assistants = $em->getRepository(AssistantHistory::class)->findByDepartmentAndSemester($department, $semester);

        $signature = $this->getDoctrine()->getRepository(Signature::class)->findByUser($this->getUser());
        $oldPath = '';
        if (null === $signature) {
            $signature = new Signature();
        } else {
            $oldPath = $signature->getSignaturePath();
        }

        $form = $this->createForm(CreateSignatureType::class, $signature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = null !== $request->files->get('create_signature')['signature_path'];

            if ($isImageUpload) {
                $signaturePath = $this->fileUploader->uploadSignature($request);
                $this->fileUploader->deleteSignature($oldPath);

                $signature->setSignaturePath($signaturePath);
            } else {
                $signature->setSignaturePath($oldPath);
            }

            $signature->setUser($this->getUser());
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($signature);
            $manager->flush();

            $this->addFlash('success', 'Signatur og evt. kommentar ble lagret');

            return $this->redirect($request->headers->get('referer'));
        }

        // Finds all the certificate requests
        $certificateRequests = $this->getDoctrine()->getRepository(CertificateRequest::class)->findAll();

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
