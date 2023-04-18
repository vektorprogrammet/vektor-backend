<?php

namespace App\Repository;

use App\Entity\PasswordReset;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PasswordResetRepository extends EntityRepository
{
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('password_reset')
            ->select('password_reset')
            ->where('password_reset.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     *
     * @return PasswordReset
     */
    public function findPasswordResetByHashedResetCode($hashedResetCode)
    {
        return $this->createQueryBuilder('PasswordReset')
            ->select('PasswordReset')
            ->where('PasswordReset.hashedResetCode = :hashedResetCode')
            ->setParameter('hashedResetCode', $hashedResetCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deletePasswordResetByHashedResetCode($hashedResetCode)
    {
        return $this->createQueryBuilder('PasswordReset')
            ->delete()
            ->where('PasswordReset.hashedResetCode = :hashedResetCode')
            ->setParameter('hashedResetCode', $hashedResetCode)
            ->getQuery()
            ->getResult();
    }
}
