<?php

namespace Smart\CoreBundle\Doctrine\RepositoryTrait;

trait FindDeleted
{
    /**
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return \Doctrine\ORM\Query
     */
    public function findDeleted(array $orderBy = null, $limit = null, $offset = null)
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('e')
            ->where('e.deleted_at IS NOT NULL')
        ;

        $firstOrderBy = true;
        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $value) {
                if ($firstOrderBy) {
                    $qb->orderBy("e.$field", $value);
                    $firstOrderBy = false;
                } else {
                    $qb->addOrderBy("e.$field", $value);
                }
            }
        }

        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }

        if (!empty($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
}
