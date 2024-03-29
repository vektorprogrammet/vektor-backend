<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'interview_question_alternative')]
#[ORM\Entity]
class InterviewQuestionAlternative
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Alternativ: Dette feltet kan ikke være tomt.')]
    protected ?string $alternative = null;

    #[ORM\ManyToOne(targetEntity: InterviewQuestion::class, inversedBy: 'alternatives')]
    #[ORM\JoinColumn(name: 'question_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?InterviewQuestion $interviewQuestion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
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
