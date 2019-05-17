<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use Doctrine\ORM\Mapping as ORM;

/**
 * IsDeleted column
 */
trait IsDeleted
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default":0})
     */
    protected $is_deleted;

    /**
     * @param boolean $is_deleted
     *
     * @return $this
     */
    public function setIsDeleted($is_deleted)
    {
        $this->is_deleted = empty($is_deleted) ? false : true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @return string
     */
    public function getIsDeletedAsText()
    {
        return $this->is_deleted ? 'Yes' : 'No';
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->is_deleted;
    }
}
