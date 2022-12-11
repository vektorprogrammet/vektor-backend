<?php

namespace App\AssistantScheduling;

use App\Entity\Application;
use JsonSerializable;

class Assistant implements JsonSerializable
{
    private string $name;
    private string $email;
    private string $assignedSchool; // Name of school
    private string $assignedDay;
    private array $availability; // An associative array. Key = weekday, Value = {0, 1, 2}. 0 is bad, 1 is ok, 2 is good. "Monday" => 2.
    /**
     * @var int
     */
    private $group;
    /**
     * @var int
     */
    private $preferredGroup;
    private bool $doublePosition;
    private bool $previousParticipation;
    private int $score;
    private string $suitability;
    private Application $application;

    /**
     * Assistant constructor.
     */
    public function __construct()
    {
        $this->group = null;
        $this->preferredGroup = null;
        $this->doublePosition = false;
        $this->availability = array();
    }

    /**
     * @return bool
     */
    public function isAssignedToSchool(): bool
    {
        return !is_null($this->assignedSchool);
    }

    public function getId(): int
    {
        return $this->application->getId();
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function isDoublePosition(): bool
    {
        return $this->doublePosition;
    }

    public function setDoublePosition(bool $doublePosition)
    {
        $this->doublePosition = $doublePosition;
    }

    public function getAvailability(): array
    {
        return $this->availability;
    }

    public function setAvailability(array $availability)
    {
        $this->availability = $availability;
    }

    public function getAssignedSchool(): string
    {
        return $this->assignedSchool;
    }

    public function setAssignedSchool(string $assignedSchool)
    {
        $this->assignedSchool = $assignedSchool;
    }

    public function getAssignedDay(): string
    {
        return $this->assignedDay;
    }

    public function setAssignedDay(string $assignedDay)
    {
        $this->assignedDay = $assignedDay;
    }

    public function getAvailable(): array
    {
        return $this->availability;
    }

    public function setAvailable(array $availability)
    {
        $this->availability = $availability;
    }

    /**
     * @return int
     */
    public function getPreferredGroup()
    {
        return $this->preferredGroup;
    }

    public function setPreferredGroup(int $preferredGroup)
    {
        $this->preferredGroup = $preferredGroup;
    }

    /**
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(int $group)
    {
        $this->group = $group;
    }

    public function isPreviousParticipation(): bool
    {
        return $this->previousParticipation;
    }

    public function setPreviousParticipation(bool $previousParticipation)
    {
        $this->previousParticipation = $previousParticipation;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score)
    {
        $this->score = $score;
    }

    public function getSuitability(): string
    {
        return $this->suitability;
    }

    public function setSuitability(string $suitability)
    {
        $this->suitability = $suitability;
    }

    public function assignToSchool(School $school, int $group, string $day)
    {
        $this->setAssignedSchool($school->getName());
        if ($this->group == 1 && $group == 2 || $this->group == 2 && $group == 1) {
            $this->group = 3;
        } else {
            $this->setGroup($group);
        }
        $this->setAssignedDay($day);
    }

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->getId(),
            'group' => 'Bolk 1',
            'name' => $this->name,
            'email' => $this->email,
            'assignedSchool' => $this->assignedSchool,
            'assignedDay' => $this->assignedDay,
            'availability' => $this->availability,
            'preferredGroup' => $this->preferredGroup,
            'doublePosition' => $this->doublePosition,
            'score' => $this->score,
            'suitable' => $this->suitability,
            'previousParticipation' => $this->previousParticipation,
            'language' => $this->application->getLanguage(),
        );
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}
