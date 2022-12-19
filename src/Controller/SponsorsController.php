<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\Type\SponsorType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SponsorsController extends BaseController
{
    public function __construct(private readonly FileUploader $fileUploader, private readonly ManagerRegistry $doctrine)
    {
    }

    public function sponsorsShow(): Response
    {
        $sponsors = $this->doctrine
            ->getRepository(Sponsor::class)
            ->findAll();

        return $this->render('sponsors/sponsors_show.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    public function sponsorEdit(Request $request, Sponsor $sponsor = null)
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
        if ($form->isSubmitted() && $form->isValid()) {
            if (!is_null($request->files->get('sponsor')['logoImagePath'])) {
                $imgPath = $this->fileUploader->uploadSponsor($request);
                $this->fileUploader->deleteSponsor($oldImgPath);

                $sponsor->setLogoImagePath($imgPath);
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

        return $this->render('sponsors/sponsor_edit.html.twig', [
            'form' => $form->createView(),
            'sponsor' => $sponsor,
            'is_create' => $isCreate,
        ]);
    }

    public function deleteSponsor(Sponsor $sponsor): RedirectResponse
    {
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
