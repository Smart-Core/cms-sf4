<?php

namespace Smart\CoreBundle\Doctrine\RepositoryTrait;

trait Count
{
    /**
     * @return int
     */
    public function count()
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('e');
        $qb->select('count(e.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
