<?php

namespace App\Twig;

use App\Entity\Sponsor;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SponsorsExtension extends AbstractExtension
{
    protected EntityManagerInterface $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getName(): string
    {
        return 'SponsorsExtension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_sponsors', [$this, 'getSponsors']),
            new TwigFunction('get_sponsors_by_size', [$this, 'getSponsorsBySize']),
        ];
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

    public function getSponsorsBySize($size): array
    {
        $sponsors = $this->doctrine
            ->getRepository(Sponsor::class)
            ->findBy(['size' => $size]);
        if (!$sponsors) {
            return [];
        }

        return $sponsors;
    }
}
