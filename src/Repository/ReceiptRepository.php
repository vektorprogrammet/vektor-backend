<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\User;
use App\Entity\Receipt;

class ReceiptRepository extends EntityRepository
{
    /**
     *
     * @return Receipt[]
     */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     *
     * @return Receipt[]
     */
    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('receipt')
            ->select('receipt')
            ->where('receipt.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}
