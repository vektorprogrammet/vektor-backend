<?php

namespace App\Controller;

use App\Entity\User;
use App\Role\Roles;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfilePhotoController extends BaseController
{
    private FileUploader $fileUploader;
    protected ManagerRegistry $doctrine;

    public function __construct(FileUploader $fileUploader, ManagerRegistry $doctrine)
    {
        parent::__construct($doctrine);
        $this->fileUploader = $fileUploader;
        $this->doctrine = $doctrine;
    }

    public function showEditProfilePhoto(User $user): Response
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('profile/edit_profile_photo.html.twig', array(
            'user' => $user,
        ));
    }

    public function editProfilePhotoUpload(User $user, Request $request): JsonResponse
    {
        $loggedInUser = $this->getUser();
        if ($user !== $loggedInUser && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw $this->createAccessDeniedException();
        }

        $picturePath = $this->fileUploader->uploadProfileImage($request);
        if (!$picturePath) {
            return new JsonResponse("Kunne ikke laste inn bildet", 400);
        }

        $this->fileUploader->deleteProfileImage($user->getPicturePath());
        $user->setPicturePath($picturePath);

        $this->doctrine->getManager()->flush();

        return new JsonResponse("Upload OK");
    }
}
