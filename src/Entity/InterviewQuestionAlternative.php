<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'interview_question_alternative')]
#[ORM\Entity]
class InterviewQuestionAlternative
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Alternativ: Dette feltet kan ikke være tomt.')]
    protected ?string $alternative = null;

    #[ORM\ManyToOne(targetEntity: 'InterviewQuestion', inversedBy: 'alternatives')]
    #[ORM\JoinColumn(name: 'question_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?InterviewQuestion $interviewQuestion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set alternative.
     *
     * @param string $alternative
     */
    public function setAlternative($alternative): self
    {
        $this->alternative = $alternative;

        return $this;
    }

    public function getAlternative(): ?string
    {
        return $this->alternative;
    }

    /**
     * Set interviewQuestion.
     */
    public function setInterviewQuestion(InterviewQuestion $interviewQuestion = null): self
    {
        $this->interviewQuestion = $interviewQuestion;

        return $this;
    }

    public function getInterviewQuestion(): ?InterviewQuestion
    {
        return $this->interviewQuestion;
    }
}
