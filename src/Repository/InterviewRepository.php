<?php

namespace App\Repository;

use App\Entity\AdmissionPeriod;
use App\Entity\Interview;
use App\Entity\InterviewStatusType;
use App\Entity\Semester;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * InterviewRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InterviewRepository extends EntityRepository
{
    /**
     * @return Interview
     */
    public function findLastScheduledByUserInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod)
    {
        $result = $this->createQueryBuilder('interview')
            ->join('interview.application', 'application')
            ->where('interview.interviewer = :user')
            ->setParameter('user', $user)
            ->andWhere('application.admissionPeriod = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->andWhere('interview.lastScheduleChanged IS NOT NULL')
            ->orderBy('interview.lastScheduleChanged', 'DESC')
            ->getQuery()
            ->getResult();

        return !empty($result) ? $result[0] : null;
    }

    public function findAllInterviewedInterviewsBySemester($semester)
    {
        $interviews = $this->getEntityManager()->createQuery('
		SELECT interview
		FROM App:Interview interview
		JOIN App:Application app
		WITH interview.application = app
		JOIN App:ApplicationStatistic appStat
		WITH app.statistic = appStat
		WHERE interview.interviewed = 1
		AND appStat.semester = :semester
		')
            ->setParameter('semester', $semester)
            ->getResult();

        return $interviews;
    }

    /**
     * @return int
     */
    public function numberOfInterviewsByUserInSemester(User $user, Semester $semester)
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT COUNT(i)
        FROM App:Interview i
        JOIN App:Application a
        WITH a.interview = i
        WHERE i.interviewer = ?1
        AND a.semester = ?2
        AND a.previousParticipation = 0
        ')
            ->setParameter(1, $user)
            ->setParameter(2, $semester);

        return $query->getSingleScalarResult();
    }

    public function findLatestInterviewByUser(User $user)
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT i
        FROM App:Interview i
        WHERE i.user = ?1
        ORDER BY i.conducted ASC
        ')
            ->setParameter(1, $user)
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * @return Interview
     */
    public function findByResponseCode(string $responseCode)
    {
        return $this->createQueryBuilder('interview')
            ->where('interview.responseCode = :responseCode')
            ->setParameter('responseCode', $responseCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Interview[]
     */
    public function findUncompletedInterviewsByInterviewerInCurrentSemester(User $interviewer)
    {
        $semester = $interviewer->getDepartment()->getCurrentAdmissionPeriod();
        if (null === $semester) {
            return [];
        }

        return $this->createQueryBuilder('interview')
            ->join('interview.application', 'application')
            ->join('application.admissionPeriod', 'admissionPeriod')
            ->where('admissionPeriod.semester = :semester')
            ->setParameter('semester', $semester)
            ->andWhere('interview.interviewer = :interviewer OR interview.coInterviewer = :interviewer')
            ->andWhere('interview.interviewed = false')
            ->setParameter('interviewer', $interviewer)
            ->orderBy('interview.scheduled')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[]
     */
    public function findInterviewersInSemester(Semester $semester)
    {
        /**
         * @var $interviews Interview[]
         */
        $interviews = $this->createQueryBuilder('interview')
                    ->join('interview.application', 'application')
                    ->join('application.admissionPeriod', 'admissionPeriod')
                    ->where('admissionPeriod.semester = :semester')
                    ->setParameter('semester', $semester)
                    ->getQuery()
                    ->getResult();
        $interviewers = [];
        foreach ($interviews as $interview) {
            $interviewers[] = $interview->getInterviewer();
            if ($interview->getCoInterviewer()) {
                $interviewers[] = $interview->getCoInterviewer();
            }
        }

        return array_unique($interviewers);
    }

    /**
     * Find interviews which will receive accept-interview notifications.
     * All interviews scheduled to a time after $time and having PENDING
     * interview status apply.
     *
     * @return array
     */
    public function findAcceptInterviewNotificationRecipients(\DateTime $time)
    {
        return $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.scheduled > :time')
            ->andWhere('i.interviewStatus = :status')
            ->setParameter('time', $time)
            ->setParameter('status', InterviewStatusType::PENDING)
            ->getQuery()
            ->getResult();
    }
}
