<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExpiresAt column
 */
trait ExpiresAt
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expires_at;

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * @param \DateTime $expires_at
     *
     * @return $this
     */
    public function setExpiresAt(\DateTime $expires_at = null)
    {
        $this->expires_at = $expires_at;

        return $this;
    }
}
