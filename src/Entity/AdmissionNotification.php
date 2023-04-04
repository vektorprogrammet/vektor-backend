<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'admission_notification')]
#[ORM\Entity(repositoryClass: 'App\Repository\AdmissionNotificationRepository')]
class AdmissionNotification
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $timestamp = null;

    #[ORM\ManyToOne(targetEntity: 'AdmissionSubscriber')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?AdmissionSubscriber $subscriber = null;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    private ?Semester $semester = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $infoMeeting = null;

    #[ORM\ManyToOne(targetEntity: 'Department')]
    private ?Department $department = null;

    public function __construct()
    {
        $this->timestamp = new \DateTime();
        $this->infoMeeting = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getSubscriber(): ?AdmissionSubscriber
    {
        return $this->subscriber;
    }

    public function setSubscriber(AdmissionSubscriber $subscriber): void
    {
        $this->subscriber = $subscriber;
    }

    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    public function setSemester(Semester $semester): void
    {
        $this->semester = $semester;
    }

    public function getInfoMeeting(): ?bool
    {
        return $this->infoMeeting;
    }

    public function setInfoMeeting(bool $bool): void
    {
        $this->infoMeeting = $bool;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): static
    {
        $this->department = $department;

        return $this;
    }
}
