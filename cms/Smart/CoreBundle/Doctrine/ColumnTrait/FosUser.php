<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use FOS\UserBundle\Model\UserInterface;

trait FosUser
{
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="FOS\UserBundle\Model\UserInterface")
     */
    protected $user;

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     *
     * @return $this
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }
}
