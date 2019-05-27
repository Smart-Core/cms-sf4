<?php

declare(strict_types=1);

namespace App\Entity;

use Monolith\CMSBundle\Model\UserModel;
use Smart\CoreBundle\Doctrine\ColumnTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends UserModel
{
    use ColumnTrait\Phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $patronymic;

    public function serialize()
    {
        return serialize([
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
            //$this->roles
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
            //$this->roles
            ) = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @param string $patronymic
     *
     * @return $this
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;

        return $this;
    }
}
