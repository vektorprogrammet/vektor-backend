<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'certificate_request')]
#[ORM\Entity(repositoryClass: 'App\Repository\CertificateRequestRepository')]
class CertificateRequest
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'certificateRequests')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set user.
     *
     * @return CertificateRequest
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    // Used for unit testing
    public function fromArray($data = []): void
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}
