<?php

namespace App\Tests\Core\Infrastructure\Persistence;

use App\Core\Domain\Entity\Semester;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SemesterRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindAll(): void
    {
        $semesters = $this->entityManager
            ->getRepository(Semester::class)
            ->findAll();

        $this->assertCount(5, $semesters);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
