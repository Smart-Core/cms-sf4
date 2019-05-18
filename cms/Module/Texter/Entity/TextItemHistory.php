<?php

namespace Monolith\Module\Texter\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Doctrine\ColumnTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="texter_history",
 *      indexes={
 *          @ORM\Index(columns={"deleted_at"}),
 *      }
 * )
 */
class TextItemHistory
{
    use ColumnTrait\Id;
    use ColumnTrait\DeletedAt;
    use ColumnTrait\CreatedAt;
    use ColumnTrait\Text;
    use ColumnTrait\FosUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=8)
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
     * @var TextItem
     *
     * @ORM\ManyToOne(targetEntity="TextItem", inversedBy="history")
     */
    protected $item;

    /**
     * ItemHistory constructor.
     *
     * @param TextItem|null $item
     */
    public function __construct(TextItem $item = null)
    {
        if ($item) {
            $this->editor   = $item->getEditor();
            $this->item_id  = $item->getId();
            $this->locale   = $item->getLocale();
            $this->meta     = $item->getMeta();
            $this->text     = $item->getText();
            $this->user     = $item->getUser();
        }

        $this->created_at   = new \DateTime();
    }

    /**
     * Получить анонс.
     *
     * @return string
     */
    public function getAnnounce()
    {
        $a = strip_tags($this->text);

        $dotted = (mb_strlen($a, 'utf-8') > 120) ? '...' : '';

        return mb_substr($a, 0, 120, 'utf-8').$dotted;
    }

    /**
     * @param int $editor
     *
     * @return $this
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return int
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param int $item_id
     *
     * @return $this
     */
    public function setItemId($item_id)
    {
        $this->item_id = $item_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->item_id;
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
     * @param array $meta
     *
     * @return $this
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return TextItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param TextItem $item
     *
     * @return $this
     */
    public function setItem(TextItem $item)
    {
        $this->item = $item;

        return $this;
    }
}
