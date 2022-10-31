<?php

namespace App\Controller;

use App\Entity\Semester;
use App\Form\Type\CreateSemesterType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SemesterController extends AbstractController
{
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function show(): Response
    {
        $semesters = $this->doctrine->getRepository(Semester::class)->findAllOrderedByAge();

        return $this->render('semester_admin/index.html.twig', array(
            'semesters' => $semesters,
        ));
    }


    public function createSemester(Request $request)
    {
        $semester = new Semester();

        // Create the form
        $form = $this->createForm(CreateSemesterType::class, $semester);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isSubmitted() && $form->isValid()) {
            //Check if semester already exists
            $existingSemester = $this->doctrine->getManager()->getRepository(Semester::class)
                ->findByTimeAndYear($semester->getSemesterTime(), $semester->getYear());

            //Return to semester page if semester already exists
            if ($existingSemester !== null) {
                $this->addFlash('warning', "Semesteret $existingSemester finnes allerede");
                return $this->redirectToRoute('semester_create');
            }

            $em = $this->doctrine->getManager();
            $em->persist($semester);
            $em->flush();

            return $this->redirectToRoute('semester_show');
        }

        // Render the view
        return $this->render('semester_admin/create_semester.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function delete(Semester $semester): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $em->remove($semester);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }
}
