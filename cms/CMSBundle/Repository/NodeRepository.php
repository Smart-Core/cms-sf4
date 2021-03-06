<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Repository;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityRepository;
use Monolith\CMSBundle\Entity\Folder;
use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Entity\Region;
use Smart\CoreBundle\Doctrine\RepositoryTrait;

class NodeRepository extends EntityRepository
{
    use RepositoryTrait\FindDeleted;

    /**
     * @param array $list
     *
     * @return Node[]
     */
    public function findIn(array $list): array
    {
        $list_string = '';
        foreach ($list as $node_id) {
            $list_string .= $node_id.',';
        }

        $list_string = substr($list_string, 0, strlen($list_string) - 1); // обрезка послезней запятой.

        if (false == $list_string) {
            return [];
        }

        return $this->_em->createQuery("
            SELECT n
            FROM CMSBundle:Node AS n
            WHERE n.id IN({$list_string})
            ORDER BY n.position ASC
        ")->getResult();
    }

    /**
     * @param Region|int $region
     *
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countInRegion($region): int
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->join('e.region', 'r')
            ->where('r.id = :region')
            ->setParameter('region', $region)
        ;

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int|Folder $folder
     * @param array $exclude_nodes
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function getInFolder($folder, array $exclude_nodes = []): Statement
    {
        if ($folder instanceof Folder) {
            $folder = $folder->getId();
        }

        $engine_nodes_table = $this->_class->getTableName();

        $sql = "
            SELECT id
            FROM $engine_nodes_table
            WHERE folder_id = '$folder'
            AND is_active = TRUE
            AND deleted_at IS NULL
        ";

        // Исключение ранее включенных нод.
        foreach ($exclude_nodes as $node_id) {
            $sql .= " AND id != '{$node_id}'";
        }

        $sql .= ' ORDER BY position';

        return $this->_em->getConnection()->query($sql);
    }

    /**
     * @param int|Folder $folder
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function getInheritedInFolder($folder): Statement
    {
        if ($folder instanceof Folder) {
            $folder = $folder->getId();
        }

        $engine_nodes_table           = $this->_class->getTableName();
        $engine_regions_inherit_table = $this->_em->getClassMetadata(Region::class)->getAssociationMapping('folders')['joinTable']['name'];

        $sql = "
            SELECT n.id
            FROM $engine_nodes_table AS n,
                $engine_regions_inherit_table AS ri
            WHERE n.region_id = ri.region_id
                AND n.is_active = TRUE
                AND n.deleted_at IS NULL
                AND n.folder_id = '$folder'
                AND ri.folder_id = '$folder'
            ORDER BY n.position ASC
        ";

        return $this->_em->getConnection()->query($sql);
    }
}
