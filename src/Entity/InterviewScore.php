<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'interview_score')]
#[ORM\Entity]
class InterviewScore
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['interview'])]
    protected ?int $explanatoryPower = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['interview'])]
    protected ?int $roleModel = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['interview'])]
    protected ?int $suitability = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['interview'])]
    private ?string $suitableAssistant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setExplanatoryPower(int $explanatoryPower): self
    {
        $this->explanatoryPower = $explanatoryPower;

        return $this;
    }

    public function getExplanatoryPower(): ?int
    {
        return $this->explanatoryPower;
    }

    public function setRoleModel(int $roleModel): self
    {
        $this->roleModel = $roleModel;

        return $this;
    }

    public function getRoleModel(): ?int
    {
        return $this->roleModel;
    }

    public function setSuitability(int $suitability): self
    {
        $this->suitability = $suitability;

        return $this;
    }

    public function getSuitability(): ?int
    {
        return $this->suitability;
    }

    public function getSum(): ?int
    {
        return $this->explanatoryPower + $this->roleModel + $this->suitability;
    }

    public function getSuitableAssistant(): ?string
    {
        return $this->suitableAssistant;
    }

    public function setSuitableAssistant(string $suitableAssistant): void
    {
        $this->suitableAssistant = $suitableAssistant;
    }

    public function hideScores(): void
    {
        $this->setExplanatoryPower(0);
        $this->setRoleModel(0);
        $this->setSuitability(0);
    }
}
