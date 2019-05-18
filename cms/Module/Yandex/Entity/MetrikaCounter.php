<?php

declare(strict_types=1);

namespace Monolith\Module\Yandex\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Doctrine\ColumnTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="yandex_metrika_counters",
 *      indexes={
 *          @ORM\Index(columns={"position"}),
 *          @ORM\Index(columns={"user_id"}),
 *      }
 * )
 */
class MetrikaCounter
{
    use ColumnTrait\Id;
    use ColumnTrait\IsEnabled;
    use ColumnTrait\CreatedAt;
    use ColumnTrait\Position;
    use ColumnTrait\Title;
    use ColumnTrait\FosUser;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     */
    protected $counter_id;

    /**
     * @var OauthToken
     *
     * @ORM\ManyToOne(targetEntity="OauthToken")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $token;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->created_at   = new \DateTime();
        $this->is_enabled   = true;
        $this->position     = 0;
    }

    /**
     * @return string
     */
    public function getCounterId(): string
    {
        return $this->counter_id;
    }

    /**
     * @param string $counter_id
     *
     * @return $this
     */
    public function setCounterId($counter_id)
    {
        $this->counter_id = $counter_id;

        return $this;
    }

    /**
     * @return OauthToken
     */
    public function getToken(): OauthToken
    {
        return $this->token;
    }

    /**
     * @param OauthToken $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
}
