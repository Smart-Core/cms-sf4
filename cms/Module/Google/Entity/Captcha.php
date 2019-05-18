<?php

declare(strict_types=1);

namespace Monolith\Module\Google\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Doctrine\ColumnTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="google_captcha")
 *
 * @UniqueEntity(fields="name", message="Имя уже занято")
 */
class Captcha
{
    use ColumnTrait\Id;
    use ColumnTrait\NameUnique;
    use ColumnTrait\Comment;
    use ColumnTrait\IsEnabled;
    use ColumnTrait\Position;
    use ColumnTrait\CreatedAt;
    use ColumnTrait\UpdatedAt;
    use ColumnTrait\FosUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $secret_key;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $site_key;

    /**
     * Captcha constructor.
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secret_key;
    }

    /**
     * @param string $secret_key
     *
     * @return $this
     */
    public function setSecretKey($secret_key): Captcha
    {
        $this->secret_key = $secret_key;

        return $this;
    }

    /**
     * @return string
     */
    public function getSiteKey(): string
    {
        return $this->site_key;
    }

    /**
     * @param string $site_key
     *
     * @return $this
     */
    public function setSiteKey($site_key): Captcha
    {
        $this->site_key = $site_key;

        return $this;
    }

}
