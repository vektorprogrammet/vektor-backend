<?php

namespace App\Repository;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Department;
use App\Entity\User;
use App\Entity\InterviewStatusType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * ApplicationInfoRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends EntityRepository
{

    /**
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application|null
     * @throws NonUniqueResultException
     */
    public function findByUserInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('application')
            ->select('application')
            ->where('application.user = :user')
            ->andWhere('application.admissionPeriod = :admissionPeriod')
            ->setParameter('user', $user)
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $user
     *
     * @return Application|null
     *
     * @throws NonUniqueResultException
     */
    public function findActiveByUser(User $user)
    {
        $department = $user->getDepartment();
        $admissionPeriod = $department->getCurrentOrLatestAdmissionPeriod();

        return $this->findByUserInAdmissionPeriod($user, $admissionPeriod);
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return array
     */
    public function findEmailsByAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        $res = $this->createQueryBuilder('application')
            ->select('user.email')
            ->join('application.user', 'user')
            ->where('application.admissionPeriod = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getArrayResult();

        return array_map(function ($row) {
            return $row["email"];
        }, $res);
    }

    /**
     * @param string $email
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findByEmailInAdmissionPeriod($email, AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('application')
            ->select('application')
            ->join('application.user', 'user')
            ->where('user.email = :email')
            ->andWhere('application.admissionPeriod = :admissionPeriod')
            ->setParameter('email', $email)
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds all applications that have a conducted interview.
     *
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findInterviewedApplicants($admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.admissionPeriod', 'ap')
            ->join('ap.department', 'd')
            ->join('a.interview', 'i')
            ->where('i.interviewed = true')
            ->andWhere('a.previousParticipation = 0')
            ->andWhere('ap = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param null $department
     * @param null $admissionPeriod
     *
     * @return Application[]
     */
    public function findPreviousApplicants($department = null, $admissionPeriod = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.admissionPeriod', 'ap')
            ->join('ap.department', 'd')
            ->andWhere('a.previousParticipation = 1');

        if (null !== $department) {
            $qb->andWhere('d = :department')
               ->setParameter('department', $department);
        }

        if (null !== $admissionPeriod) {
            $qb->andWhere('ap = :admissionPeriod')
               ->setParameter('admissionPeriod', $admissionPeriod);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Finds all applications that have been assigned an interview that has not yet been conducted.
     *
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findAssignedApplicants($admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.admissionPeriod', 'ap')
            ->join('ap.department', 'd')
            ->join('a.user', 'u')
            ->join('a.interview', 'i')
            ->where('i.interviewed = 0')
            ->andWhere('i.interviewStatus is NULL OR NOT i.interviewStatus = :status')
            ->andWhere('ap = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->setParameter('status', InterviewStatusType::CANCELLED)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findAssignedByUserAndAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->join('a.interview', 'i')
            ->where('a.admissionPeriod = :admissionPeriod')
            ->andWhere('i.interviewed = 0')
            ->andWhere('i.interviewStatus is NULL OR NOT i.interviewStatus = :status')
            ->andWhere('i.interviewer = :user OR i.coInterviewer = :user')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->setParameter('status', InterviewStatusType::CANCELLED)
            ->setParameter('user', $user)
            ->orderBy('i.scheduled', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findCancelledApplicants(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.admissionPeriod', 'ap')
            ->join('a.interview', 'i')
            ->where('ap = :admissionPeriod')
            ->andWhere('i.interviewStatus = :status')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->setParameter('status', InterviewStatusType::CANCELLED)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds all applications without an interview, or without a conducted interview.
     *
     * @param null $department
     * @param null $admissionPeriod
     *
     * @return Application[]
     */
    public function findNewApplicants($department = null, $admissionPeriod = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.admissionPeriod', 'ap')
            ->join('ap.department', 'd')
            ->join('a.user', 'u')
            ->leftJoin('a.interview', 'i')
            ->where('a.previousParticipation = 0')
            ->andWhere('i is NULL OR i.interviewed = 0')
            ->andWhere('i.interviewStatus is NULL OR NOT i.interviewStatus = :status')
            ->setParameter('status', InterviewStatusType::CANCELLED);

        if (null !== $department) {
            $qb->andWhere('d = :department')
               ->setParameter('department', $department);
        }

        if (null !== $admissionPeriod) {
            $qb->andWhere('ap = :admissionPeriod')
               ->setParameter('admissionPeriod', $admissionPeriod);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return array
     */
    public function findNewApplicationsByAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.interview', 'i')
            ->where('a.previousParticipation = 0')
            ->andWhere('i is NULL OR i.interviewed = 0')
            ->andWhere('i.interviewStatus is NULL OR NOT i.interviewStatus = :status')
            ->andWhere('a.admissionPeriod = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->setParameter('status', InterviewStatusType::CANCELLED)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findExistingApplicants(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.admissionPeriod', 'ap')
            ->where('a.previousParticipation = 1')
            ->andWhere('ap = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return array
     */
    public function findApplicationByTeamInterestAndAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('application')
            ->select('application')
            ->where('application.teamInterest = :teamInt')
            ->andWhere('application.admissionPeriod = :ap')
            ->setParameter('teamInt', true)
            ->setParameter('ap', $admissionPeriod)
            ->getQuery()
            ->getResult();
    }

    public function findApplicantById($id)
    {
        return $this->createQueryBuilder('Application')
            ->select('Application')
            ->where('Application.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findApplicantStatisticById($id)
    {
        return $this->createQueryBuilder('ApplicationStatistic')
            ->select('ApplicationStatistic')
            ->where('ApplicationStatistic.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return int
     */
    public function numOfApplications(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('Application')
            ->select('count(Application.id)')
            ->where('Application.admissionPeriod = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     * @param int $gender
     *
     * @return int
     */
    public function numOfGender(AdmissionPeriod $admissionPeriod, $gender)
    {
        return $this->createQueryBuilder('Application')
            ->select('count(Application.id)')
            ->join('Application.user', 'user')
            ->where('user.gender = :gender')
            ->andWhere('Application.admissionPeriod = :admissionPeriod')
            ->setParameter('gender', $gender)
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return int
     */
    public function numOfPreviousParticipation(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('Application')
            ->select('count(Application.id)')
            ->where('Application.previousParticipation = 1')
            ->andWhere('Application.admissionPeriod = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function numOfAccepted($accepted)
    {
        return $this->createQueryBuilder('ApplicationStatistic')
            ->select('count(ApplicationStatistic.accepted)')
            ->where('ApplicationStatistic.accepted = :accepted')
            ->setParameter('accepted', $accepted)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function numOfYearOfStudy($yearOfStudy)
    {
        return $this->createQueryBuilder('ApplicationStatistic')
            ->select('count(ApplicationStatistic.yearOfStudy)')
            ->where('ApplicationStatistic.yearOfStudy = :yearOfStudy')
            ->setParameter('yearOfStudy', $yearOfStudy)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function numOfFieldOfStudy($fieldOfStudy)
    {
        return $this->createQueryBuilder('ApplicationStatistic')
            ->select('count(ApplicationStatistic.fieldOfStudy)')
            ->where('ApplicationStatistic.fieldOfStudy = :fieldOfStudy')
            ->setParameter('fieldOfStudy', $fieldOfStudy)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function numOfDepartment($department)
    {
        $numUsers = $this->getEntityManager()->createQuery('

		SELECT COUNT (AppS.id)
		FROM App:ApplicationStatistic AppS
		JOIN AppS.admissionPeriod ap
		JOIN ap.department d
		WHERE d.id = :department

		')
                         ->setParameter('department', $department)
                         ->getSingleScalarResult();

        return $numUsers;
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findAllAllocatableApplicationsByAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.interview', 'i')
            ->where('a.admissionPeriod = :admissionPeriod')
            ->andWhere('a.previousParticipation = 1 OR i.interviewed = 1')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findSubstitutesByAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('Application')
            ->select('Application')
            ->where('Application.admissionPeriod = :admissionPeriod')
            ->andWhere('Application.substitute = TRUE')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Department $department
     *
     * @return Application[]
     */
    public function findByDepartment(Department $department)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.admissionPeriod', 'ap')
            ->where('ap.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application[]
     */
    public function findByAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.admissionPeriod = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->orderBy('a.created', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
