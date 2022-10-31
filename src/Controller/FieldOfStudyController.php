<?php

namespace App\Controller;

use App\Entity\FieldOfStudy;
use App\Form\Type\FieldOfStudyType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FieldOfStudyController extends BaseController
{
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct($doctrine);
        $this->doctrine = $doctrine;
    }

    public function show(): Response
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        $fieldOfStudies = $this->doctrine->getRepository(FieldOfStudy::class)->findByDepartment($department);

        return $this->render('field_of_study/show_all.html.twig', array(
            'fieldOfStudies' => $fieldOfStudies,
            'department' => $department,
        ));
    }

    public function edit(Request $request, FieldOfStudy $fieldOfStudy = null)
    {
        $isEdit = true;
        if ($fieldOfStudy === null) {
            $fieldOfStudy = new FieldOfStudy();
            $isEdit = false;
        } else {
            // Check if user is trying to edit FOS from department other than his own
            if ($fieldOfStudy->getDepartment() !== $this->getUser()->getFieldOfStudy()->getDepartment()) {
                throw new AccessDeniedException();
            }
        }
        $form = $this->createForm(FieldOfStudyType::class, $fieldOfStudy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fieldOfStudy->setDepartment($this->getUser()->getFieldOfStudy()->getDepartment());
            $manager = $this->doctrine->getManager();
            $manager->persist($fieldOfStudy);
            $manager->flush();

            return $this->redirectToRoute('show_field_of_studies');
        }

        return $this->render('field_of_study/form.html.twig', array(
            'form' => $form->createView(),
            'isEdit' => $isEdit,
            'fieldOfStudy' => $fieldOfStudy,
        ));
    }
}
