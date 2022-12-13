<?php

namespace App\Controller;

use App\Entity\Position;
use App\Form\Type\CreatePositionType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends BaseController
{
    public function showPositions(): Response
    {
        // Find all the positions
        $positions = $this->getDoctrine()->getRepository(Position::class)->findAll();

        // Return the view with suitable variables
        return $this->render('team_admin/show_positions.html.twig', [
            'positions' => $positions,
        ]);
    }

    public function editPosition(Request $request, Position $position = null)
    {
        $isCreate = null === $position;
        if ($isCreate) {
            $position = new Position();
        }

        $form = $this->createForm(CreatePositionType::class, $position);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($position);
            $em->flush();

            $flash = 'Stillingen ble ';
            $flash .= $isCreate ? 'opprettet.' : 'endret.';

            $this->addFlash('success', $flash);

            return $this->redirectToRoute('teamadmin_show_position');
        }

        return $this->render('team_admin/create_position.html.twig', [
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'position' => $position,
        ]);
    }

    public function removePosition(Position $position): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($position);
        $em->flush();

        $this->addFlash('success', 'Stillingen ble slettet.');

        return $this->redirectToRoute('teamadmin_show_position');
    }
}
