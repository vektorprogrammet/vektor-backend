<?php

namespace App\Tests\Core\Application\UseCase;

use App\Core\Domain\Entity\Semester;
use App\Core\Application\UseCase\SemesterUseCase;
use App\Core\Application\UseCase\Interfaces\Persistence\ISemesterRepository;
use App\Core\Application\Util\SemesterUtil;
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
        
        $semesterUseCase = new SemesterUseCase($semesterRepository);
        $semesters = $semesterUseCase->getAllSemesters();
        $this->assertCount(4, $semesters);
    }

    public function testGetCurrentSemester(){
        $repositorySemester = SemesterUtil::timeToSemester(new \DateTime());

        $semesterRepository = $this->createMock(ISemesterRepository::class);
        $semesterRepository->expects($this->once())
            ->method('findSemesterByDate')
            ->willReturn($repositorySemester);
        
        $semesterUseCase = new SemesterUseCase($semesterRepository);
        $semester = $semesterUseCase->getCurrentSemester();
        $this->assertEquals($repositorySemester, $semester);
    }
}