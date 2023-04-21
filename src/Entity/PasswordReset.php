<?php

namespace App\Entity;

use App\Repository\PasswordResetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'password_reset')]
#[ORM\Entity(repositoryClass: PasswordResetRepository::class)]
class PasswordReset
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'user', referencedColumnName: 'id')]
    protected ?User $user = null;

    #[ORM\Column(type: 'string')]
    protected ?string $hashedResetCode = null;

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $resetTime = null;

    private ?string $resetCode = null;

    public function __construct()
    {
        $this->setResetTime(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setHashedResetCode(string $hashedResetCode): self
    {
        $this->hashedResetCode = $hashedResetCode;

        return $this;
    }

    public function getHashedResetCode(): ?string
    {
        return $this->hashedResetCode;
    }

    /**
     * @param \DateTime $resetTime
     */
    public function setResetTime($resetTime): self
    {
        $this->resetTime = $resetTime;

        return $this;
    }

    public function getResetTime(): ?\DateTime
    {
        return $this->resetTime;
    }

    /**
     * @return PasswordReset
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getResetCode(): ?string
    {
        return $this->resetCode;
    }

    public function setResetCode(string $resetCode): void
    {
        $this->resetCode = $resetCode;
    }
}
