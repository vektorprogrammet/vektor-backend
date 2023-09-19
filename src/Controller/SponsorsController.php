<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\Type\SponsorType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SponsorsController extends AbstractController
{
    public function __construct(
        private readonly FileUploader $fileUploader,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    /**
     * Page for showing all sponsors.
     */
    #[Route('/kontrollpanel/sponsorer', name: 'sponsors_show', methods: ['GET'])]
    public function sponsorsShow(): Response
    {
        $sponsors = $this->doctrine
            ->getRepository(Sponsor::class)
            ->findAll();

        return $this->render('sponsors/sponsors_show.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    /**
     * Page for creating and editing sponsors.
     */
    public function sponsorEdit(Request $request, Sponsor $sponsor = null): RedirectResponse|Response
    {
        $isCreate = $sponsor === null;

        $oldImgPath = '';
        if ($isCreate) {
            $sponsor = new Sponsor();
        } else {
            $oldImgPath = $sponsor->getLogoImagePath();
        }

        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        // If the form is submitted and valid, save the sponsor and redirect to the sponsors page.
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if a new image was uploaded.
            $fileUpload = $request->files->get('sponsor')['logoImagePath'];

            // If a new image was uploaded, upload it and delete the old one.
            if ($fileUpload) {
                $imgPath = $this->fileUploader->uploadSponsor($request);
                $this->fileUploader->deleteSponsor($oldImgPath);

                $sponsor->setLogoImagePath($imgPath);
                // Else use the old image.
            } else {
                $sponsor->setLogoImagePath($oldImgPath);
            }

            $em = $this->doctrine->getManager();
            $em->persist($sponsor);
            $em->flush();

            $this->addFlash(
                'success',
                "Sponsor {$sponsor->getName()} ble " . ($isCreate ? 'opprettet' : 'endret')
            );

            return $this->redirectToRoute('sponsors_show');
        }

        // Else: Render the edit/create sponsor form.
        return $this->render('sponsors/sponsor_edit.html.twig', [
            'form' => $form->createView(),
            'sponsor' => $sponsor,
            'is_create' => $isCreate,
        ]);
    }

    /**
     * Page for deleting a sponsor.
     */
    #[Route('/kontrollpanel/sponsor/delete/{id}',
        name: 'sponsor_delete',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST'])]
    public function sponsorDelete(Sponsor $sponsor): RedirectResponse
    {
        // Delete the sponsor's logo image.
        if ($sponsor->getLogoImagePath()) {
            $this->fileUploader->deleteSponsor($sponsor->getLogoImagePath());
        }

        $em = $this->doctrine->getManager();
        $em->remove($sponsor);
        $em->flush();

        $this->addFlash('success', "Sponsor {$sponsor->getName()} ble slettet.");

        return $this->redirectToRoute('sponsors_show');
    }
}
