<?php

namespace App\Repository;

use App\Entity\StaticContent;
use Doctrine\ORM\EntityRepository;

/**
 * StaticContentRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StaticContentRepository extends EntityRepository
{
    /**
     * @param string $htmlId
     *
     * @return StaticContent
     */
    public function findOneByHtmlId(string $htmlId)
    {
        return $this->createQueryBuilder('sc')
            ->where('sc.htmlId = :htmlId')
            ->setParameter('htmlId', $htmlId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
