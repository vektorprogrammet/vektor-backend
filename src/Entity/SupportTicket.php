<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class SupportTicket
{
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[Assert\NotBlank]
    private ?string $subject = null;

    #[Assert\NotBlank]
    private ?string $body = null;

    #[Assert\NotNull(message: 'Klarte ikke sende melding til denne avdelingen. Send oss en mail isteden.')]
    #[Assert\Valid]
    private ?Department $department = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody($body): void
    {
        $this->body = $body;
    }

    // Used for unit testing the forms
    public function fromArray($data = []): void
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
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
        return
           "Name: $this->name\n" .
           "Email: $this->email\n" .
           "Subject: $this->subject\n" .
           "Body: $this->body\n" .
           "Department: $this->department";
    }
}
