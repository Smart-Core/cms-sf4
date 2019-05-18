<?php

declare(strict_types=1);

namespace Monolith\Module\Texter\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Doctrine\ColumnTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="texter_items",
 *      indexes={
 *          @ORM\Index(columns={"created_at"}),
 *      }
 * )
 */
class TextItem
{
    use ColumnTrait\Id;
    use ColumnTrait\CreatedAt;
    use ColumnTrait\UpdatedAt;
    use ColumnTrait\Text;
    use ColumnTrait\FosUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    protected $locale;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    protected $editor;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $meta;

    /**
     * @var TextItemHistory[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TextItemHistory", mappedBy="item", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    protected $history;

    /**
     * TextItem constructor.
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->locale   = 'ru';
        $this->meta     = [];
        $this->text     = null;
        $this->editor   = 1;
        $this->history  = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getText();
    }

    /**
     * @return string
     */
    public function getAnnounce(): string
    {
        $a = strip_tags((string) $this->text);

        $dotted = (mb_strlen($a, 'utf-8') > 100) ? '...' : '';

        return mb_substr($a, 0, 100, 'utf-8').$dotted;
    }

    /**
     * @param int $editor
     *
     * @return $this
     */
    public function setEditor(int $editor): TextItem
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return int
     */
    public function getEditor(): int
    {
        return $this->editor;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return empty($this->meta) ? [] : $this->meta;
    }

    /**
     * @param array $meta
     *
     * @return $this
     */
    public function setMeta(array $meta)
    {
        if (is_array($meta)) {
            foreach ($meta as $key => $value) {
                if (empty($value)) {
                    unset($meta[$key]);
                }
            }
        } else {
            $this->meta = [];
        }

        $this->meta = $meta;

        return $this;
    }

    /**
     * @return TextItemHistory[]|ArrayCollection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param TextItemHistory[]|ArrayCollection $history
     *
     * @return $this
     */
    public function setHistory($history)
    {
        $this->history = $history;

        return $this;
    }
}
