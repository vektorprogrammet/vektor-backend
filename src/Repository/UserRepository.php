<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    public function findUsersInDepartmentWithTeamMembershipInSemester(Department $department, Semester $semester)
    {
        $users = $this->createQueryBuilder('user')
            ->select('user')
            ->join('user.teamMemberships', 'tm')
            ->join('tm.team', 'team')
            ->where('team.department = :department')
            ->join('tm.startSemester', 'ss')
            ->leftJoin('tm.endSemester', 'se')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();

        $teamMembers = [];
        /** @var User $user */
        foreach ($users as $user) {
            foreach ($user->getTeamMemberships() as $teamMembership) {
                if ($semester->isBetween(
                    $teamMembership->getStartSemester(),
                    $teamMembership->getEndSemester()
                )
                ) {
                    $teamMembers[] = $user;
                    continue 2;
                }
            }
        }

        return $teamMembers;
    }

    /**
     * @return User[]
     */
    public function findUsersWithAssistantHistoryInDepartmentAndSemester(Department $department, Semester $semester)
    {
        return $this->createQueryBuilder('user')
            ->select('user')
            ->join('user.assistantHistories', 'ah')
            ->where('ah.department = :department')
            ->andWhere('ah.semester = :semester')
            ->setParameters([
                'department' => $department,
                'semester' => $semester,
            ])
            ->getQuery()
            ->getResult();
    }

    public function findAllUsersByDepartment($department)
    {
        // TODO: Refactor to use QueryBuilder
        $users = $this->getEntityManager()->createQuery('
		
		SELECT u
		FROM App:User u
		JOIN u.fieldOfStudy fos
		JOIN fos.department d
		WHERE d.id = :department
		
		')
            ->setParameter('department', $department)
            ->getResult();

        return $users;
    }

    public function findAllActiveUsersByDepartment($department)
    {
        // TODO: Refactor to use QueryBuilder
        $users = $this->getEntityManager()->createQuery('
		
		SELECT u
		FROM App:User u
		JOIN u.fieldOfStudy fos
		JOIN fos.department d
		WHERE d.id = :department
			AND u.isActive = :active
		')
            ->setParameter('department', $department)
            ->setParameter('active', 1)
            ->getResult();

        return $users;
    }

    public function findAllInActiveUsersByDepartment($department)
    {
        return $this->createQueryBuilder('user')
            ->select('user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('user.isActive = false')
            ->andWhere('fos.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    public function findAllUsersWithReceipts()
    {
        return $this->createQueryBuilder('user')
            ->select('user')
            ->join('user.receipts', 'receipt')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     *
     * @return User
     */
    public function findByUsernameOrEmail($login)
    {
        return $this->createQueryBuilder('User')
                    ->select('User')
                    ->where('User.user_name = :username')
                    ->setParameter('username', $login)
                    ->orWhere('User.email = :email')
                    ->setParameter('email', $login)
                    ->orWhere('User.companyEmail = :companyEmail')
                    ->setParameter('companyEmail', $login)
                    ->getQuery()
                    ->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     *
     * @return User
     */
    public function findUserByEmail($email)
    {
        return $this->createQueryBuilder('User')
            ->select('User')
            ->where('User.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     *
     * @return User
     */
    public function findUserByNewUserCode($id)
    {
        return $this->createQueryBuilder('User')
            ->select('User')
            ->where('User.new_user_code = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllCompanyEmails()
    {
        $results = $this->createQueryBuilder('user')
            ->select('user.companyEmail')
            ->where('user.companyEmail IS NOT NULL')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'companyEmail');
    }

    /*
    These functions are used by UserProviderInterface
    */

    public function loadUserByUsername($username): UserInterface
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.user_name = :user_name OR u.email = :email')
            ->setParameter('user_name', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin VektorVektorBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $class = $user::class;
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class): bool
    {
        return $this->getEntityName() === $class
        || is_subclass_of($class, $this->getEntityName());
    }

    public function findAssistants()
    {
        return $this->createQueryBuilder('user')
            ->join('user.assistantHistories', 'ah')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    public function findTeamMembers()
    {
        return $this->createQueryBuilder('user')
                    ->join('user.teamMemberships', 'tm')
                    ->distinct()
                    ->getQuery()
                    ->getResult();
    }
}
