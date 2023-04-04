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
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $timestamp;

    #[ORM\ManyToOne(targetEntity: 'AdmissionSubscriber')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $subscriber;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    private $semester;

    #[ORM\Column(type: 'boolean')]
    private $infoMeeting;

    /**
     * @var Department
     */
    #[ORM\ManyToOne(targetEntity: 'Department')]
    private $department;

    public function __construct()
    {
        $this->timestamp = new \DateTime();
        $this->infoMeeting = false;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return AdmissionSubscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param AdmissionSubscriber $subscriber
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @return Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @param Semester $semester
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;
    }

    /**
     * @return bool
     */
    public function getInfoMeeting()
    {
        return $this->infoMeeting;
    }

    /**
     * @param bool $bool
     */
    public function setInfoMeeting($bool)
    {
        $this->infoMeeting = $bool;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @return AdmissionNotification
     */
    public function setDepartment(Department $department)
    {
        $this->department = $department;

        return $this;
    }
}
