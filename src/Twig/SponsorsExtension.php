<?php

namespace App\Twig;

use App\Entity\Sponsor;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SponsorsExtension extends AbstractExtension
{
    protected $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getName()
    {
        return 'SponsorsExtension';
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('get_sponsors', [$this, 'getSponsors']),
            new TwigFunction('get_sponsors_by_size', [$this, 'getSponsorsBySize']),
        );
    }

    public function getSponsors()
    {
        $sponsors = $this->doctrine
            ->getRepository(Sponsor::class)
            ->findAll();
        if (!$sponsors) {
            return 'No sponsors :-(';
        }

        return $sponsors;
    }

    public function getSponsorsBySize($size)
    {
        $sponsors = $this->doctrine
            ->getRepository(Sponsor::class)
            ->findBy(array('size' => $size));
        if (!$sponsors) {
            return [];
        }

        return $sponsors;
    }
}
