<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class SupportTicket
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $body;

    /**
     * @var Department
     *
     * @Assert\NotNull(message="Klarte ikke sende melding til denne avdelingen. Send oss en mail isteden.")
     * @Assert\Valid()
     */
    private $department;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    // Used for unit testing the forms
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    public function setDepartment(Department $department)
    {
        $this->department = $department;
    }

    public function __toString()
    {
        return
           "Name: $this->name\n".
           "Email: $this->email\n".
           "Subject: $this->subject\n".
           "Body: $this->body\n".
           "Department: $this->department";
    }
}
