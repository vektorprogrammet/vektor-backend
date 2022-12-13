<?php

namespace App\Repository;

use App\Entity\Receipt;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class ReceiptRepository extends EntityRepository
{
    /**
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
