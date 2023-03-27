<?php

namespace App\Twig;

use App\Entity\Sponsor;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SponsorsExtension extends AbstractExtension
{
    public function __construct(protected EntityManagerInterface $doctrine)
    {
    }

    public function getName(): string
    {
        return 'SponsorsExtension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_sponsors', $this->getSponsors(...)),
            new TwigFunction('get_sponsors_by_size', $this->getSponsorsBySize(...)),
        ];
    }

    public function getSponsors(): array
    {
        return $this->doctrine
            ->getRepository(Sponsor::class)
            ->findAll();
    }

    public function getSponsorsBySize($size): array
    {
        return $this->doctrine
            ->getRepository(Sponsor::class)
            ->findBy(['size' => $size]);
    }
}
