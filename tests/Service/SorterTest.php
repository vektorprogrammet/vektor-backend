<?php

namespace App\Tests\Service;

use App\Entity\Receipt;
use App\Entity\User;
use App\Service\Sorter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SorterTest extends KernelTestCase
{
    private ?object $sorter = null;

    /** @var User[] */
    private array $mockUsers;

    /** @var Receipt[] */
    private array $mockReceipts;

    protected function setUp(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) get service from the container
        $sorter = $container->get(Sorter::class);

        $this->sorter = $sorter;

        $this->mockReceipts = [
            new Receipt(),
            new Receipt(),
            new Receipt(),
            new Receipt(),
        ];

        $this->mockUsers = [
            new User(),
            new User(),
            new User(),
            new User(), // has no receipts
        ];

        $this->mockReceipts[0]->setSubmitDate(new \DateTime('2015-09-05'));
        $this->mockReceipts[1]->setSubmitDate(new \DateTime('2016-09-05'));
        $this->mockReceipts[2]->setSubmitDate(new \DateTime('2017-09-05'));
        $this->mockReceipts[3]->setSubmitDate(new \DateTime('2017-09-05'));

        $this->mockReceipts[0]->setStatus(Receipt::STATUS_REFUNDED);
        $this->mockReceipts[1]->setStatus(Receipt::STATUS_PENDING);
        $this->mockReceipts[2]->setStatus(Receipt::STATUS_REJECTED);
        $this->mockReceipts[3]->setStatus(Receipt::STATUS_PENDING);

        foreach ($this->mockReceipts as $receipt) {
            $this->mockUsers[0]->addReceipt($receipt);
        }

        $user2Receipt = new Receipt();
        $user3Receipt = new Receipt();

        $user2Receipt->setStatus(Receipt::STATUS_REFUNDED);
        $user3Receipt->setStatus(Receipt::STATUS_PENDING);

        $user3Receipt->setSubmitDate(new \DateTime('2013-09-05'));

        $this->mockUsers[1]->addReceipt($user2Receipt);
        $this->mockUsers[2]->addReceipt($user3Receipt);
    }

    public function testSortReceiptsBySubmitTime()
    {
        $this->sorter->sortReceiptsBySubmitTime($this->mockReceipts);
        $this->assertEquals(new \DateTime('2017-09-05'), $this->mockReceipts[0]->getSubmitDate());
        $this->assertEquals(new \DateTime('2017-09-05'), $this->mockReceipts[1]->getSubmitDate());
        $this->assertEquals(new \DateTime('2016-09-05'), $this->mockReceipts[2]->getSubmitDate());
        $this->assertEquals(new \DateTime('2015-09-05'), $this->mockReceipts[3]->getSubmitDate());
    }

    public function testSortReceiptsByStatus()
    {
        $this->sorter->sortReceiptsBySubmitTime($this->mockReceipts); // Sort them by submit time first, to get predictable results
        $this->sorter->sortReceiptsByStatus($this->mockReceipts);
        $this->assertEquals(Receipt::STATUS_PENDING, $this->mockReceipts[0]->getStatus());
        $this->assertEquals(Receipt::STATUS_PENDING, $this->mockReceipts[1]->getStatus());
        $this->assertEquals(Receipt::STATUS_REJECTED, $this->mockReceipts[2]->getStatus()); // Newer
        $this->assertEquals(Receipt::STATUS_REFUNDED, $this->mockReceipts[3]->getStatus()); // Older
    }

    public function testSortUsersByReceiptSubmitTime()
    {
        $this->sorter->sortUsersByReceiptSubmitTime($this->mockUsers);

        $firstDate = $this->mockUsers[0]->getReceipts()[0]->getSubmitDate();
        $secondsSinceFirstDate = abs((new \DateTime())->getTimestamp() - $firstDate->getTimestamp());

        $this->assertLessThan(1, $secondsSinceFirstDate); // Newest = now
        $this->assertEquals($this->mockReceipts, $this->mockUsers[1]->getReceipts()->toArray()); // Newest = 2017
        $this->assertEquals(new \DateTime('2013-09-05'), $this->mockUsers[2]->getReceipts()[0]->getSubmitDate()); // Newest = 2013
        $this->assertEmpty($this->mockUsers[3]->getReceipts()->toArray()); // Fourth user has no receipts
    }

    public function testSortUsersByReceiptStatus()
    {
        $this->sorter->sortUsersByReceiptSubmitTime($this->mockUsers); // Sort by submit time to get predictable results
        $this->sorter->sortUsersByReceiptStatus($this->mockUsers);
        $this->assertEquals($this->mockReceipts, $this->mockUsers[0]->getReceipts()->toArray()); // Newer
        $this->assertEquals(Receipt::STATUS_PENDING, $this->mockUsers[1]->getReceipts()[0]->getStatus()); // Older
        $this->assertEquals(Receipt::STATUS_REFUNDED, $this->mockUsers[2]->getReceipts()[0]->getStatus());
        $this->assertEmpty($this->mockUsers[3]->getReceipts()->toArray()); // Fourth user has no receipts
    }
}
