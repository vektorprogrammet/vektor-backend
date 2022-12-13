<?php

namespace App\Tests\Entity;

use App\Entity\InterviewQuestion;
use App\Entity\InterviewQuestionAlternative;
use PHPUnit\Framework\TestCase;

class InterviewQuestionEntityUnitTest extends TestCase
{
    public function testSetQuestion()
    {
        $intQuestion = new InterviewQuestion();

        $intQuestion->setQuestion('Test?');

        $this->assertSame('Test?', $intQuestion->getQuestion());
    }

    public function testSetHelp()
    {
        $intQuestion = new InterviewQuestion();

        $intQuestion->setHelp('Test');

        $this->assertSame('Test', $intQuestion->getHelp());
    }

    public function testSetType()
    {
        $intQuestion = new InterviewQuestion();

        $intQuestion->setType('Text');

        $this->assertSame('Text', $intQuestion->getType());
    }

    public function testAddAlternative()
    {
        $intQuestion = new InterviewQuestion();
        $alternative = new InterviewQuestionAlternative();

        $this->assertSame(0, \count($intQuestion->getAlternatives()));

        $intQuestion->addAlternative($alternative);

        $this->assertSame(1, \count($intQuestion->getAlternatives()));
    }

    public function testRemoveAlternative()
    {
        $intQuestion = new InterviewQuestion();
        $alternative = new InterviewQuestionAlternative();

        $intQuestion->addAlternative($alternative);

        $this->assertContains($alternative, $intQuestion->getAlternatives());

        $intQuestion->removeAlternative($alternative);

        $this->assertNotContains($alternative, $intQuestion->getAlternatives());
    }
}
