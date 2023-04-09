<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'interview_question')]
#[ORM\Entity]
class InterviewQuestion
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 5000)]
    #[Assert\NotBlank(message: 'Spørsmål: Dette feltet kan ikke være tomt.')]
    #[Assert\Length(max: 5000, maxMessage: 'Spørsmål: Maks 5000 tegn')]
    protected ?string $question = null;

    #[ORM\Column(type: 'string', length: 5000, nullable: true)]
    protected ?string $help = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'interviewQuestion', targetEntity: 'InterviewQuestionAlternative', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    protected $alternatives;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set question.
     */
    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->alternatives = new ArrayCollection();
    }

    /**
     * Set help.
     */
    public function setHelp(string $help): self
    {
        $this->help = $help;

        return $this;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    /**
     * Set type.
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Add alternatives.
     */
    public function addAlternative(InterviewQuestionAlternative $alternatives): self
    {
        $this->alternatives[] = $alternatives;

        $alternatives->setInterviewQuestion($this);

        return $this;
    }

    /**
     * Remove alternatives.
     */
    public function removeAlternative(InterviewQuestionAlternative $alternatives): void
    {
        $this->alternatives->removeElement($alternatives);

        $alternatives->setInterviewQuestion(null);
    }

    /**
     * Get alternatives.
     *
     * @return Collection
     */
    public function getAlternatives()
    {
        return $this->alternatives;
    }
}
