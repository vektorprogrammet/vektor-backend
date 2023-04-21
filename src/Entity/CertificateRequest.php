<?php

namespace App\Entity;

use App\Repository\CertificateRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'certificate_request')]
#[ORM\Entity(repositoryClass: CertificateRequestRepository::class)]
class CertificateRequest
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'certificateRequests')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
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
