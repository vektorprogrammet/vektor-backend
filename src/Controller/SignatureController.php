<?php

namespace App\Controller;

use App\Entity\Signature;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SignatureController extends BaseController
{
    public function showSignatureImage($imageName): BinaryFileResponse
    {
        $user = $this->getUser();

        $signature = $this->getDoctrine()->getRepository(Signature::class)->findByUser($user);
        if ($signature === null) {
            throw new NotFoundHttpException('Signature not found');
        }

        $signatureImagePath = $signature->getSignaturePath();
        $signatureFileName = mb_substr((string) $signatureImagePath, mb_strrpos((string) $signatureImagePath, '/') + 1);
        if ($imageName !== $signatureFileName) {
            // Users can only view their own signatures
            throw new AccessDeniedException();
        }

        return new BinaryFileResponse($this->container->getParameter('signature_images') . '/' . $signatureFileName);
    }
}
