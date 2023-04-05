<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'interview_schema')]
#[ORM\Entity]
class InterviewSchema
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $name = null;

    #[ORM\JoinTable(name: 'interview_schemas_questions')]
    #[ORM\JoinColumn(name: 'schema_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'question_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'InterviewQuestion', cascade: ['persist'])]
    #[Assert\Valid]
    protected $interviewQuestions; // Unidirectional, may turn out to be bidirectional

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->interviewQuestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function addInterviewQuestion(InterviewQuestion $questions): self
    {
        $this->interviewQuestions[] = $questions;

        return $this;
    }

    public function removeInterviewQuestion(InterviewQuestion $questions): void
    {
        $this->interviewQuestions->removeElement($questions);
    }

    /**
     * Get questions.
     *
     * @return Collection
     */
    public function getInterviewQuestions()
    {
        return $this->interviewQuestions;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return InterviewSchema
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
