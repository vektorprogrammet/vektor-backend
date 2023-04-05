<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'password_reset')]
#[ORM\Entity(repositoryClass: 'App\Repository\PasswordResetRepository')]
class PasswordReset
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'user', referencedColumnName: 'id')]
    protected $user;

    #[ORM\Column(type: 'string')]
    protected ?string $hashedResetCode = null;

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $resetTime = null;

    private ?string $resetCode = null;

    /**
     * PasswordReset constructor.
     */
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
     * Set resetTime.
     *
     * @param \DateTime $resetTime
     *
     * @return PasswordReset
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
     * Set user.
     *
     * @return PasswordReset
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

    public function getResetCode(): ?string
    {
        return $this->resetCode;
    }

    /**
     * @param string $resetCode
     */
    public function setResetCode(string $resetCode): void
    {
        $this->resetCode = $resetCode;
    }
}
