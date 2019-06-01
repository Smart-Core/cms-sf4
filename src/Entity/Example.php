<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExampleRepository")
 * @ORM\Table(name="example",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"title"}),
 *      }
 * )
 */
class Example
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * Example constructor.
     *
     * @param string|null $title
     */
    public function __construct(?string $title)
    {
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
