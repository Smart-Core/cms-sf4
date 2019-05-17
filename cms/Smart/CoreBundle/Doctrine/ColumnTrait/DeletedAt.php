<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeletedAt column
 */
trait DeletedAt
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted_at;

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * @param \DateTime $deleted_at
     *
     * @return $this
     */
    public function setDeletedAt(\DateTime $deleted_at = null)
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @param boolean $is_deleted
     *
     * @return $this
     */
    public function setIsDeleted($is_deleted)
    {
        $this->deleted_at = $is_deleted ? new \DateTime() : null;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->deleted_at ? true : false;
    }

    /**
     * @return string
     */
    public function getIsDeletedAsText()
    {
        return $this->deleted_at ? 'Yes' : 'No';
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted_at ? true : false;
    }
}
