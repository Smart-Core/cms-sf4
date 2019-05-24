<?php

declare(strict_types=1);

namespace Monolith\Module\Menu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Doctrine\ColumnTrait;
use Monolith\CMSBundle\Entity\Folder;

/**
 * @ORM\Entity(repositoryClass="MenuItemRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="menu_items")
 */
class MenuItem
{
    use ColumnTrait\Id;
    use ColumnTrait\CreatedAt;
    use ColumnTrait\UpdatedAt;
    use ColumnTrait\IsActive;
    use ColumnTrait\Description;
    use ColumnTrait\Position;
    use ColumnTrait\Title;
    use ColumnTrait\FosUser;

    /**
     * @var MenuItem
     *
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="pid")
     */
    protected $parent_item;

    /**
     * @var MenuItem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent_item")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $children;

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="items")
     */
    protected $menu;

    /**
     * @var Folder
     *
     * @ORM\ManyToOne(targetEntity="Monolith\CMSBundle\Entity\Folder")
     */
    protected $folder;

    /**
     * Custom url.
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default":0})
     */
    protected $open_in_new_window;

    /**
     * @var string
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $properties;

    /**
     * Для отображения в формах. Не маппится в БД.
     */
    protected $form_title = '';

    /**
     * MenuItem constructor.
     *
     * @param Menu|null $menu
     */
    public function __construct(Menu $menu = null)
    {
        $this->children          = new ArrayCollection();
        $this->created_at        = new \DateTime();
        $this->is_active         = true;
        $this->open_in_new_window = false;
        $this->parent_item       = null;
        $this->position          = 0;
        $this->properties        = null;
        $this->title             = null;
        $this->url               = null;
        $this->updated_at        = null;

        if ($menu) {
            $this->menu = $menu;
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $title = $this->getTitle();
        if (empty($title)) {
            $title = (null === $this->getFolder()) ? $this->getId() : $this->getFolder()->getTitle();
        }

        return (string) $title;
    }

    /**
     * @param Folder|null $folder
     *
     * @return $this
     */
    public function setFolder(Folder $folder = null): self
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * @return Folder|null
     */
    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    /**
     * @param string $form_title
     *
     * @return $this
     */
    public function setFormTitle($form_title): self
    {
        $this->form_title = $form_title;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormTitle(): string
    {
        return $this->form_title;
    }

    /**
     * @return Menu
     */
    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     *
     * @return $this
     */
    public function setMenu(Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * @return MenuItem[]|ArrayCollection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param array|null $properties
     *
     * @return $this
     */
    public function setProperties(array $properties = null): self
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getProperties(): array
    {
        return empty($this->properties) ? [] : $this->properties;
    }

    /**
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl($url = null): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param bool $open_in_new_window
     *
     * @return $this
     */
    public function setOpenInNewWindow($open_in_new_window): self
    {
        $this->open_in_new_window = $open_in_new_window;

        return $this;
    }

    /**
     * @return bool
     */
    public function getOpenInNewWindow(): bool
    {
        return $this->open_in_new_window;
    }

    /**
     * @param MenuItem|null $parent_item
     *
     * @return $this
     */
    public function setParentItem($parent_item): self
    {
        if (empty($parent_item) or $parent_item->getId() == $this->getId()) {
            $this->parent_item = null;
        } else {
            $this->parent_item = $parent_item;
        }

        return $this;
    }

    /**
     * @return MenuItem|null
     */
    public function getParentItem(): ?self
    {
        return $this->parent_item;
    }

    /**
     * Защита от цикличных зависимостей.
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function checkRelations(): void
    {
        $parent = $this->getParentItem();

        if (null == $parent) {
            return;
        }

        $cnt = 30;
        $ok = false;
        while ($cnt--) {
            if (null == $parent or $parent->getId() == 1) {
                $ok = true;
                break;
            } else {
                $parent = $parent->getParentItem();
                continue;
            }
        }

        // Если обнаружена циклическая зависимость, тогда родитель выставляется корневая папка, которая имеет id = 1.
        if (!$ok) {
            $this->setParentItem(null);
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onUpdated(): void
    {
        $this->updated_at = new \DateTime();
    }
}
