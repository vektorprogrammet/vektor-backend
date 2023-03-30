<?php

namespace App\Controller;

use App\Entity\FieldOfStudy;
use App\Form\Type\FieldOfStudyType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FieldOfStudyController extends BaseController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * Show all Field of Studies.
     */
    public function show(): Response
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        $fieldOfStudies = $this->doctrine->getRepository(FieldOfStudy::class)->findByDepartment($department);

        return $this->render('field_of_study/show_all.html.twig', [
            'fieldOfStudies' => $fieldOfStudies,
            'department' => $department,
        ]);
    }

    /**
     * Create or edit Field of Study.
     */
    public function edit(Request $request, FieldOfStudy $fieldOfStudy = null): RedirectResponse|Response
    {
        $isEdit = $fieldOfStudy !== null;
        $userDepartment = $this->getUser()->getFieldOfStudy()->getDepartment();

        // Create new Field of Study if not editing
        if (!$isEdit) {
            $fieldOfStudy = new FieldOfStudy();
        }

        // User is not allowed to edit other departments' Field of Studies
        elseif ($fieldOfStudy->getDepartment() !== $userDepartment) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(FieldOfStudyType::class, $fieldOfStudy)->handleRequest($request);

        // If form is submitted and valid, save FoS and redirect
        if ($form->isSubmitted() && $form->isValid()) {
            $fieldOfStudy->setDepartment($userDepartment);
            $manager = $this->doctrine->getManager();
            $manager->persist($fieldOfStudy);
            $manager->flush();

            return $this->redirectToRoute('show_field_of_studies');
        }

        // Render create/edit FoS form
        return $this->render('field_of_study/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => $isEdit,
            'fieldOfStudy' => $fieldOfStudy,
        ]);
    }
}
