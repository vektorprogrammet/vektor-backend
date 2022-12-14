<?php

namespace App\Twig;

use App\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SemesterExtension extends AbstractExtension
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getName(): string
    {
        return 'semester_extension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_semesters', [$this, 'getSemesters']),
        ];
    }

    public function getSemesters()
    {
        return $this->em->getRepository(Semester::class)->findAllOrderedByAge();
    }
}
