<?php

namespace App\Twig;

use App\Entity\Department;
use App\Service\GeoLocation;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DepartmentExtension extends AbstractExtension
{
    public function __construct(
        private readonly GeoLocation $geoLocationService,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function getName(): string
    {
        return 'department_extension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_departments', $this->getDepartments(...)),
            new TwigFunction('get_active_departments', $this->getActiveDepartments(...)),
        ];
    }

    public function getDepartments(): array
    {
        $departments = $this->em->getRepository(Department::class)->findAll();

        return $this->geoLocationService->sortDepartmentsByDistanceFromClient($departments);
    }

    public function getActiveDepartments(): array
    {
        $departments = $this->em->getRepository(Department::class)->findActive();

        return $this->geoLocationService->sortDepartmentsByDistanceFromClient($departments);
    }
}
