<?php

namespace App\Tests\Entity;

use App\Entity\Receipt;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ReceiptEntityUnitTest extends TestCase
{
    // Test the setUser() method
    public function testSetUser()
    {
        // New entities
        $user = new User();
        $receipt = new Receipt();

        // Use the setUser method
        $receipt->setUser($user);

        // Assert the result
        $this->assertSame($user, $receipt->getUser());
    }

    public function testSetSubmitDate()
    {
        $dateTime = new \DateTime();
        $receipt = new Receipt();

        $receipt->setSubmitDate($dateTime);

        $this->assertSame($dateTime, $receipt->getSubmitDate());
    }

    public function testSetPicturePath()
    {
        $picturePath = 'test';
        $receipt = new Receipt();

        $receipt->setPicturePath($picturePath);

        $this->assertSame($picturePath, $receipt->getPicturePath());
    }

    public function testSetDescription()
    {
        $sum = 13.0;
        $receipt = new Receipt();

        $receipt->setSum($sum);

        $this->assertSame($sum, $receipt->getSum());
    }

    public function testSetStatus()
    {
        $receipt = new Receipt();

        $receipt->setStatus(Receipt::STATUS_PENDING);

        $this->assertSame(Receipt::STATUS_PENDING, $receipt->getStatus());
    }
}
