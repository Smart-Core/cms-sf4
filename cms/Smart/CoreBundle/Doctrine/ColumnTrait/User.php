<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use Symfony\Component\Security\Core\User\UserInterface;

trait User
{
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
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
