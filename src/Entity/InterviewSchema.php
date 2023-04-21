<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'interview_schema')]
#[ORM\Entity]
class InterviewSchema
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $name = null;

    #[ORM\JoinTable(name: 'interview_schemas_questions')]
    #[ORM\JoinColumn(name: 'schema_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'question_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: InterviewQuestion::class, cascade: ['persist'])]
    #[Assert\Valid]
    protected Collection $interviewQuestions; // Unidirectional, may turn out to be bidirectional

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
        $this->interviewQuestions->add($questions);

        return $this;
    }

    public function removeInterviewQuestion(InterviewQuestion $questions): void
    {
        $this->interviewQuestions->removeElement($questions);
    }

    public function getInterviewQuestions(): Collection
    {
        return $this->interviewQuestions;
    }

    /**
     * @param string $name
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
