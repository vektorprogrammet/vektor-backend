<?php

namespace App\Repository;

use App\Entity\Signature;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class SignatureRepository extends EntityRepository
{
    /**
     * @return Signature
     */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('signature')
            ->select('signature')
            ->where('signature.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
