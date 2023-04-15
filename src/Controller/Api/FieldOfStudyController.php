<?php

namespace App\Controller\Api;

use App\Entity\FieldOfStudy;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FieldOfStudyController extends AbstractController
{
    public function  __construct(private readonly ManagerRegistry $doctrine)
    {

    }
    #[Route('/api/field-of-studies', name: 'api_field_of_study', methods: ['GET'])]
    public function getFieldOfStudiesForDepartment(): JsonResponse
    {
        $fieldOfStudies = $this->doctrine->getRepository(FieldOfStudy::class)->findAll();

        $fieldOfStudyDTOs = [];

        foreach ($fieldOfStudies as $fieldOfStudy) {
            $fieldOfStudyDTOs[] = [
                'id' => $fieldOfStudy->getId(),
                'name' => $fieldOfStudy->getName(),
                'department' => $fieldOfStudy->getDepartment()->getName(),
            ];
        }

        return new JsonResponse($fieldOfStudyDTOs);
    }

}