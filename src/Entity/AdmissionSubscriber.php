<?php

namespace App\Entity;

use App\Repository\AdmissionSubscriberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'admission_subscriber')]
#[ORM\Entity(repositoryClass: AdmissionSubscriberRepository::class)]
#[UniqueEntity(fields: ['unsubscribeCode'])]
class AdmissionSubscriber
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'E-post må fylles inn')]
    #[Assert\Email(message: 'Dette er ikke en gyldig e-postadresse')]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $timestamp = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    private ?Department $department = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $unsubscribeCode = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $fromApplication = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $infoMeeting = null;

    public function __construct()
    {
        $this->fromApplication = false;
        $this->infoMeeting = false;
        $this->timestamp = new \DateTime();
        $this->unsubscribeCode = bin2hex(openssl_random_pseudo_bytes(12));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getUnsubscribeCode(): ?string
    {
        return $this->unsubscribeCode;
    }

    public function setUnsubscribeCode(string $unsubscribeCode): void
    {
        $this->unsubscribeCode = $unsubscribeCode;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): void
    {
        $this->department = $department;
    }

    public function __toString()
    {
        return $this->department->getCity();
    }

    public function getFromApplication(): ?bool
    {
        return $this->fromApplication;
    }

    public function setFromApplication(bool $fromApplication): void
    {
        $this->fromApplication = $fromApplication;
    }

    public function getInfoMeeting(): ?bool
    {
        return $this->infoMeeting;
    }

    public function setInfoMeeting(bool $infoMeeting): void
    {
        $this->infoMeeting = $infoMeeting;
    }
}
