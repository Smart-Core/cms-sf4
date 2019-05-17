<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

/**
 * Date column
 */
trait Date
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date;

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate(\DateTime $date = null)
    {
        $this->date = $date;

        return $this;
    }
}
