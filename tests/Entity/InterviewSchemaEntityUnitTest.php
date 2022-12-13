<?php

namespace App\Tests\Entity;

use App\Entity\InterviewQuestion;
use App\Entity\InterviewSchema;
use PHPUnit\Framework\TestCase;

class InterviewSchemaEntityUnitTest extends TestCase
{
    public function testAddInterviewQuestion()
    {
        $intSchema = new InterviewSchema();
        $intQuestion = new InterviewQuestion();

        $this->assertNotContains($intQuestion, $intSchema->getInterviewQuestions());

        $intSchema->addInterviewQuestion($intQuestion);

        $this->assertContains($intQuestion, $intSchema->getInterviewQuestions());
    }

    public function testRemoveInterviewQuestion()
    {
        $intSchema = new InterviewSchema();
        $intQuestion = new InterviewQuestion();

        $intSchema->addInterviewQuestion($intQuestion);

        $this->assertContains($intQuestion, $intSchema->getInterviewQuestions());

        $intSchema->removeInterviewQuestion($intQuestion);

        $this->assertNotContains($intQuestion, $intSchema->getInterviewQuestions());
    }

    public function testSetName()
    {
        $intSchema = new InterviewSchema();

        $intSchema->setName('Test');

        $this->assertSame('Test', $intSchema->getName());
    }
}
