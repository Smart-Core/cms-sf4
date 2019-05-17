<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Position column
 */
trait Position
{
    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     * @Assert\Range(min = "0", max = "255")
     */
    protected $position;

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        if (empty($position)) {
            $position = 0;
        }

        $this->position = $position;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
