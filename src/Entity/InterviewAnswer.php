<?php

namespace App\Entity;

use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'interview_answer')]
#[ORM\Entity]
class InterviewAnswer
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'Interview', inversedBy: 'interviewAnswers')]
    #[ORM\JoinColumn(name: 'interview_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?Interview $interview = null;

    #[ORM\ManyToOne(targetEntity: 'InterviewQuestion')]
    #[ORM\JoinColumn(name: 'question_id', referencedColumnName: 'id')]
    protected ?InterviewQuestion $interviewQuestion = null;

    #[CustomAssert\InterviewAnswer(groups: ['interview'])]
    #[ORM\Column(type: 'array', nullable: true)]
    protected $answer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    public function setInterview(Interview $interview = null): self
    {
        $this->interview = $interview;

        return $this;
    }

    public function getInterview(): ?Interview
    {
        return $this->interview;
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

    public function __toString(): string
    {
        if (is_string($this->answer)) {
            return $this->answer;
        }
        if (!is_array($this->answer)) {
            return '';
        }

        $answerString = '';
        foreach ($this->answer as $a) {
            if (!empty($answerString)) {
                $answerString .= ', ';
            }
            $answerString .= $a;
        }

        return $answerString;
    }
}
