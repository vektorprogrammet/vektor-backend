<?php

namespace App\Tests\Core\Application\UseCase;

use App\Core\Application\DTO\SemesterDTO;
use App\Core\Application\UseCase\Interfaces\Persistence\ISemesterRepository;
use App\Core\Application\UseCase\SemesterUseCase;
use App\Core\Application\Util\SemesterUtil;
use App\Core\Domain\Entity\Semester;
use PHPUnit\Framework\TestCase;

class SemesterUseCaseTest extends TestCase
{
    public function testGetAllSemesters(): void
    {
        $semesterRepository = $this->createMock(ISemesterRepository::class);
        $semesterRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                new Semester(1, 2020, 1),
                new Semester(2, 2020, 2),
                new Semester(3, 2021, 1),
                new Semester(4, 2021, 2),
            ]);

        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $semesterUseCase = new SemesterUseCase($semesterRepository, $logger);
        $semesters = $semesterUseCase->getAllSemesters();
        $this->assertCount(4, $semesters);
    }

    public function testGetCurrentSemester()
    {
        $repositorySemester = SemesterUtil::timeToSemester(new \DateTime());

        $semesterRepository = $this->createMock(ISemesterRepository::class);
        $semesterRepository->expects($this->once())
            ->method('findSemesterByDate')
            ->willReturn($repositorySemester);

        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $semesterUseCase = new SemesterUseCase($semesterRepository, $logger);
        $semester = $semesterUseCase->getCurrentSemester();
        $this->assertEquals(SemesterDTO::createFromEntity($repositorySemester), $semester);
    }
}
