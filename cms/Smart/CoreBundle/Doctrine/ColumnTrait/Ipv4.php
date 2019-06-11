<?php

declare(strict_types=1);

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * IP v4 address column with validator
 */
trait Ipv4
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Ip(version="4")
     */
    protected $ipv4;

    /**
     * @return string|null
     */
    public function getIpv4(): ?string
    {
        return $this->ipv4;
    }

    /**
     * @param string|null $ipv4
     *
     * @return $this
     */
    public function setIpv4($ipv4): self
    {
        $this->ipv4 = $ipv4;

        return $this;
    }
}
