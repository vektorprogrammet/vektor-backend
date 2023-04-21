<?php

namespace App\Entity;

use App\Repository\SignatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'signature')]
#[ORM\Entity(repositoryClass: SignatureRepository::class)]
class Signature
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'signature_path', type: Types::STRING, length: 45, nullable: true)]
    private ?string $signaturePath = null;

    #[ORM\Column(type: Types::STRING, length: 250)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Length(min: 1, max: 250, maxMessage: 'Beskrivelsen kan maks være 250 tegn.')]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    #[Assert\Length(min: 1, max: 500, maxMessage: 'Kommentaren kan maks være 500 tegn.')]
    private ?string $additional_comment = null;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected ?User $user = null;

    public function getSignaturePath(): ?string
    {
        return $this->signaturePath;
    }

    public function setSignaturePath(string $signaturePath): void
    {
        $this->signaturePath = $signaturePath;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAdditionalComment(): ?string
    {
        return $this->additional_comment;
    }

    public function setAdditionalComment(string $additional_comment): void
    {
        $this->additional_comment = $additional_comment;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
