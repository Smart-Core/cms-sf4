<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Title column with NotBlank validator
 */
trait TitleNotBlank
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = trim($title);

        return $this;
    }
}
