<?php

namespace App\Controller;

use App\Entity\Signature;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SignatureController extends BaseController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function showSignatureImage($imageName): BinaryFileResponse
    {
        $user = $this->getUser();

        $signature = $this->doctrine->getRepository(Signature::class)->findByUser($user);
        if ($signature === null) {
            throw new NotFoundHttpException('Signature not found');
        }

        $signatureImagePath = $signature->getSignaturePath();
        $signatureFileName = substr($signatureImagePath, strrpos($signatureImagePath, '/') + 1);
        if ($imageName !== $signatureFileName) {
            // Users can only view their own signatures
            throw new AccessDeniedException();
        }

        return new BinaryFileResponse($this->container->getParameter('signature_images').'/'.$signatureFileName);
    }
}
