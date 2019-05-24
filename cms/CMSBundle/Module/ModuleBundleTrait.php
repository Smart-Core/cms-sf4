<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Module;

use Knp\Menu\MenuItem;
use Monolith\CMSBundle\Entity\Node;

trait ModuleBundleTrait
{
    /**
     * @deprecated подумать где конфигурировать админское меню модуля.
     */
    protected $adminMenuBeforeCode = '<i class="fa fa-angle-right"></i>';

    /** @var bool */
    protected $is_enabled;

    /**
     * Удобочитаемый заголовок модуля для вывода в пользовательских интерфейсах.
     *
     * @var string
     */
    protected $title;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    /**
     * @param bool $is_enabled
     *
     * @return $this
     */
    public function setIsEnabled(bool $is_enabled)
    {
        $this->is_enabled = $is_enabled;

        return $this;
    }

    /**
     * Получить виджеты для рабочего стола.
     *
     * @return array
     *
     * @deprecated
     */
    public function getDashboard()
    {
        return [];
    }

    /**
     * Получить оповещения.
     *
     * @return array
     */
    public function getNotifications(): array
    {
        return [];
    }

    /**
     * @return array
     *
     * @deprecated - должно быть в контроллере
     */
    public function getWidgets()
    {
        return [];
    }

    /**
     * Получить короткое имя (без суффикса ModuleBundle).
     *
     * @return string
     */
    final public function getShortName(): string
    {
        return substr($this->getName(), 0, -12);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        if (empty($this->title)) {
            return $this->getShortName();
        }

        return $this->title;
    }

    /**
     * Есть ли у модуля административный раздел.
     *
     * @return bool
     *
     * @deprecated - определять по наличию админских контроллеров
     */
    final public function hasAdmin(): bool
    {
        return $this->container->has('cms.router_module.'.strtolower($this->getShortName()).'.admin') ? true : false;
    }

    /**
     * @param MenuItem $menu
     * @param array $extras
     *
     * @return MenuItem
     */
    public function buildAdminMenu(MenuItem $menu, array $extras = []): MenuItem
    {
        if ($this->hasAdmin()) {
            if (!isset($extras['beforeCode'])) {
                $extras['beforeCode'] = $this->adminMenuBeforeCode;
            }

            $menu->addChild($this->getShortName(), [
                'uri' => $this->container->get('router')->generate('cms_admin.index').$this->getShortName().'/',
            ])->setExtras($extras);
        }

        return $menu;
    }
}
