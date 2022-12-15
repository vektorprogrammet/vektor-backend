<?php

namespace App\Controller;

use App\Entity\User;
use App\Role\Roles;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfilePhotoController extends BaseController
{
    public function __construct(private readonly FileUploader $fileUploader)
    {
    }

    public function showEditProfilePhoto(User $user): Response
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('profile/edit_profile_photo.html.twig', [
            'user' => $user,
        ]);
    }

    public function editProfilePhotoUpload(User $user, Request $request): JsonResponse
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        $picturePath = $this->fileUploader->uploadProfileImage($request);
        if (!$picturePath) {
            return new JsonResponse('Kunne ikke laste inn bildet', 400);
        }

        $this->fileUploader->deleteProfileImage($user->getPicturePath());
        $user->setPicturePath($picturePath);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('Upload OK');
    }
}
