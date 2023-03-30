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
    protected $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Alternativ: Dette feltet kan ikke være tomt.')]
    protected $alternative;

    #[ORM\ManyToOne(targetEntity: 'InterviewQuestion', inversedBy: 'alternatives')]
    #[ORM\JoinColumn(name: 'question_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected $interviewQuestion;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set alternative.
     *
     * @param string $alternative
     *
     * @return InterviewQuestionAlternative
     */
    public function setAlternative($alternative)
    {
        $this->alternative = $alternative;

        return $this;
    }

    /**
     * Get alternative.
     *
     * @return string
     */
    public function getAlternative()
    {
        return $this->alternative;
    }

    /**
     * Set interviewQuestion.
     *
     *
     * @return InterviewQuestionAlternative
     */
    public function setInterviewQuestion(InterviewQuestion $interviewQuestion = null)
    {
        $this->interviewQuestion = $interviewQuestion;

        return $this;
    }

    /**
     * Get interviewQuestion.
     *
     * @return InterviewQuestion
     */
    public function getInterviewQuestion()
    {
        return $this->interviewQuestion;
    }
}
