<?php


namespace App\Controller;

use App\Entity\ChangeLogItem;
use App\Form\Type\ChangeLogType;
use Symfony\Component\HttpFoundation\Request;

class ChangeLogController extends BaseController
{
    public function createChangeLog(Request $request)
    {
        $changeLogItem = new ChangeLogItem();
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($changeLogItem);
            $em->flush();

            return $this->redirect($this->generateUrl('changelog_show_all'));
        }

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ));
    }

    public function editChangeLog(Request $request, ChangeLogItem $changeLogItem)
    {
        $form = $this->createForm(ChangeLogType::class, $changeLogItem);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($changeLogItem);
            $em->flush();

            return $this->redirect($this->generateUrl('changelog_show_all'));
        }

        return $this->render('changelog/changelog_create.html.twig', array(
            'form' => $form->createView(),
            'changelog' => $changeLogItem,
        ));
    }

    public function deleteChangeLog(ChangeLogItem $changeLogItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($changeLogItem);
        $em->flush();

        $this->addFlash("success", "\"".$changeLogItem->getTitle()."\" ble slettet");

        return $this->redirect($this->generateUrl('changelog_show_all'));
    }

    public function show()
    {
        $em = $this->getDoctrine()->getManager();
        $changeLogItems = $em->getRepository(ChangeLogItem::class)->findAllOrderedByDate();
        $changeLogItems = array_reverse($changeLogItems);

        return $this->render('changelog/changelog_show_all.html.twig', array('changeLogItems' => $changeLogItems));
    }
}
