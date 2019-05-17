<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

trait UserId
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $user_id;

    /**
     * @param int|object $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        if (is_object($user_id) and method_exists($user_id, 'getId')) {
            $user_id = $user_id->getId();
        }

        if (null === $user_id) {
            $user_id = 0;
        }

        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
