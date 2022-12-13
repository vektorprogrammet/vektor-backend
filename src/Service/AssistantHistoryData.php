<?php

namespace App\Service;

use App\Entity\AssistantHistory;
use App\Entity\Department;
use App\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssistantHistoryData
{
    private $assistantHistoryRepository;
    private $semester;
    private $department;

    /**
     * AssistantHistoryData constructor.
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $ts, GeoLocation $geoLocation)
    {
        $this->assistantHistoryRepository = $em->getRepository(AssistantHistory::class);
        $user = $ts->getToken()->getUser();
        $departments = $em->getRepository(Department::class)->findAll();
        if ('anon.' === $user) {
            $this->department = $geoLocation->findNearestDepartment($departments);
        } else {
            $this->department = $ts->getToken()->getUser()->getDepartment();
        }
        $this->semester = $em->getRepository(Semester::class)->findOrCreateCurrentSemester();
    }

    /**
     * @return $this
     */
    public function setSemester(Semester $semester): self
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * @param Department $department
     */
    public function setDepartment($department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getAssistantHistoryCount(): int
    {
        return \count($this->assistantHistoryRepository->findByDepartmentAndSemester($this->department, $this->semester));
    }

    public function getCount(): int
    {
        return $this->getAssistantHistoryCount();
    }

    public function getMaleCount(): int
    {
        return $this->assistantHistoryRepository->numMaleBySemester($this->semester);
    }

    public function getFemaleCount(): int
    {
        return $this->assistantHistoryRepository->numFemaleBySemester($this->semester);
    }

    public function getPositionsCount(): int
    {
        $assistantHistories = $this->assistantHistoryRepository->findByDepartmentAndSemester($this->department, $this->semester);
        $positionsCount = \count($assistantHistories);
        foreach ($assistantHistories as $assistant) {
            if ('Bolk 1, Bolk 2' === $assistant->getBolk()) {
                ++$positionsCount;
            }
        }

        return $positionsCount;
    }
}
