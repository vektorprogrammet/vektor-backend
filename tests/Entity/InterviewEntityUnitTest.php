<?php

namespace App\Tests\Entity;

use App\Entity\Interview;
use App\Entity\InterviewAnswer;
use App\Entity\InterviewSchema;
use App\Entity\InterviewScore;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class InterviewEntityUnitTest extends TestCase
{
    public function testSetInterviewSchema()
    {
        $interview = new Interview();
        $intSchema = new InterviewSchema();

        $interview->setInterviewSchema($intSchema);

        $this->assertSame($intSchema, $interview->getInterviewSchema());
    }

    public function testSetInterviewer()
    {
        $interview = new Interview();
        $interviewer = new User();

        $interview->setInterviewer($interviewer);

        $this->assertSame($interviewer, $interview->getInterviewer());
    }

    public function testAddInterviewAnswer()
    {
        $interview = new Interview();
        $answer = new InterviewAnswer();

        $interview->addInterviewAnswer($answer);

        $this->assertContains($answer, $interview->getInterviewAnswers());
    }

    public function testRemoveInterviewAnswer()
    {
        $interview = new Interview();
        $answer = new InterviewAnswer();

        $interview->addInterviewAnswer($answer);

        $this->assertContains($answer, $interview->getInterviewAnswers());

        $interview->removeInterviewAnswer($answer);

        $this->assertNotContains($answer, $interview->getInterviewAnswers());
    }

    public function testSetInterviewScore()
    {
        $interview = new Interview();
        $intScore = new InterviewScore();

        $interview->setInterviewScore($intScore);

        $this->assertSame($intScore, $interview->getInterviewScore());
    }

    public function testSetInterviewed()
    {
        $interview = new Interview();

        $interview->setInterviewed(true);

        $this->assertTrue($interview->getInterviewed());
    }

    public function testSetScheduled()
    {
        $interview = new Interview();
        $date = new \DateTime();

        $interview->setScheduled($date);

        $this->assertSame($date, $interview->getScheduled());
    }

    public function testSetConducted()
    {
        $interview = new Interview();
        $date = new \DateTime();

        $interview->setConducted($date);

        $this->assertSame($date, $interview->getConducted());
    }
}
