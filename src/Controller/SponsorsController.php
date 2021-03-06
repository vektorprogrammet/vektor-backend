<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\Type\SponsorType;
use App\Service\FileUploader;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SponsorsController extends BaseController
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader){
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/kontrollpanel/sponsorer", name="sponsors_show")
     *
     * @return Response
     */
    public function sponsorsShow()
    {
        $sponsors = $this->getDoctrine()
            ->getRepository(Sponsor::class)
            ->findAll();

        return $this->render('sponsors/sponsors_show.html.twig', array(
            'sponsors' => $sponsors,
        ));
    }

    /**
     * @Route("/kontrollpanel/sponsor/create", name="sponsor_create")
     * @Route("/kontrollpanel/sponsor/edit/{id}", name="sponsor_edit")
     * @param Sponsor|null $sponsor
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function sponsorEdit(Sponsor $sponsor = null, Request $request)
    {
        $isCreate = $sponsor === null;
        $oldImgPath = "";
        if ($isCreate) {
            $sponsor = new Sponsor();
        } else {
            $oldImgPath = $sponsor->getLogoImagePath();
        }

        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!is_null($request->files->get('sponsor')['logoImagePath'])) {
                $imgPath = $this->fileUploader->uploadSponsor($request);
                $this->fileUploader->deleteSponsor($oldImgPath);

                $sponsor->setLogoImagePath($imgPath);
            } else {
                $sponsor->setLogoImagePath($oldImgPath);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($sponsor);
            $em->flush();

            $this->addFlash(
                "success",
                "Sponsor {$sponsor->getName()} ble " . ($isCreate ? "opprettet" : "endret")
            );

            return $this->redirectToRoute("sponsors_show");
        }

        return $this->render("sponsors/sponsor_edit.html.twig", [
            "form" => $form->createView(),
            "sponsor" => $sponsor,
            "is_create" => $isCreate
        ]);
    }

    /**
     * @Route("/kontrollpanel/sponsor/delete/{id}", name="sponsor_delete")
     * @param Sponsor $sponsor
     *
     * @return RedirectResponse
     */
    public function deleteSponsor(Sponsor $sponsor)
    {
        if ($sponsor->getLogoImagePath()) {
            $this->fileUploader->deleteSponsor($sponsor->getLogoImagePath());
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($sponsor);
        $em->flush();

        $this->addFlash("success", "Sponsor {$sponsor->getName()} ble slettet.");
        return $this->redirectToRoute("sponsors_show");
    }
}
