<?php

namespace App\Controller;

use App\AssistantScheduling\Assistant;
use App\AssistantScheduling\School;
use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\SchoolCapacity;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AssistantSchedulingController extends BaseController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    public function index(): Response
    {
        return $this->render('assistant_scheduling/index.html.twig');
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getAssistants(): JsonResponse
    {
        $user = $this->getUser();

        $currentSemester = $this->getCurrentSemester();
        $currentAdmissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($user->getDepartment(), $currentSemester);

        $applications = $this->doctrine
            ->getRepository(Application::class)
            ->findAllAllocatableApplicationsByAdmissionPeriod($currentAdmissionPeriod);

        $assistants = $this->getAssistantAvailableDays($applications);

        return new JsonResponse(json_encode($assistants, JSON_THROW_ON_ERROR));
    }

    /**
     * @param Application[] $applications
     *
     * @return array
     */
    private function getAssistantAvailableDays($applications)
    {
        $assistants = [];
        foreach ($applications as $application) {
            $doublePosition = $application->getDoublePosition();
            $preferredGroup = null;
            switch ($application->getPreferredGroup()) {
                case 'Bolk 1': $preferredGroup = 1;
                    break;
                case 'Bolk 2': $preferredGroup = 2;
                    break;
            }
            if ($doublePosition) {
                $preferredGroup = null;
            }

            $availability = [];
            $availability['Monday'] = $application->isMonday();
            $availability['Tuesday'] = $application->isTuesday();
            $availability['Wednesday'] = $application->isWednesday();
            $availability['Thursday'] = $application->isThursday();
            $availability['Friday'] = $application->isFriday();

            $assistant = new Assistant();
            $assistant->setName($application->getUser()->getFullName());
            $assistant->setEmail($application->getUser()->getEmail());
            $assistant->setDoublePosition($doublePosition);
            $assistant->setPreferredGroup($preferredGroup);
            $assistant->setAvailability($availability);
            $assistant->setApplication($application);
            if ($application->getPreviousParticipation()) {
                $assistant->setSuitability('Ja');
                $assistant->setScore(20);
            } else {
                $assistant->setScore($application->getInterview()->getScore());
                $assistant->setSuitability($application->getInterview()->getInterviewScore()->getSuitableAssistant());
            }
            $assistant->setPreviousParticipation($application->getPreviousParticipation());
            $assistants[] = $assistant;
        }

        return $assistants;
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getSchools(): JsonResponse
    {
        $user = $this->getUser();
        $department = $user->getFieldOfStudy()->getDepartment();
        $currentSemester = $this->getCurrentSemester();
        $allCurrentSchoolCapacities = $this->doctrine
            ->getRepository(SchoolCapacity::class)
            ->findByDepartmentAndSemester($department, $currentSemester);

        $schools = $this->generateSchoolsFromSchoolCapacities($allCurrentSchoolCapacities);

        return new JsonResponse(json_encode($schools, JSON_THROW_ON_ERROR));
    }

    /**
     * @param SchoolCapacity[] $schoolCapacities
     */
    private function generateSchoolsFromSchoolCapacities(array $schoolCapacities): array
    {
        // Use schoolCapacities to create School objects for the SA-Algorithm
        $schools = [];
        foreach ($schoolCapacities as $sc) {
            $capacityDays = [];
            $capacityDays['Monday'] = $sc->getMonday();
            $capacityDays['Tuesday'] = $sc->getTuesday();
            $capacityDays['Wednesday'] = $sc->getWednesday();
            $capacityDays['Thursday'] = $sc->getThursday();
            $capacityDays['Friday'] = $sc->getFriday();

            $capacity = [];
            $capacity[1] = $capacityDays;
            $capacity[2] = $capacityDays;

            $school = new School($capacity, $sc->getSchool()->getName(), $sc->getId());
            $schools[] = $school;
        }

        return $schools;
    }
}
