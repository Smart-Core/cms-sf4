<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

/**
 * Comment column
 */
trait Comment
{
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = trim($comment);

        return $this;
    }
}
