<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

/**
 * Description column
 */
trait Description
{
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = trim($description);

        return $this;
    }
}
