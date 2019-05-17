<?php

namespace Smart\CoreBundle\Doctrine\ColumnTrait;

/**
 * IsEnabled column
 */
trait IsEnabled
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true,  options={"default":1})
     */
    protected $is_enabled;

    /**
     * @param bool $is_enabled
     * @return $this
     */
    public function setIsEnabled($is_enabled)
    {
        $this->is_enabled = $is_enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->is_enabled;
    }

    /**
     * @return string
     */
    public function getIsEnabledAsText()
    {
        return $this->is_enabled ? 'Yes' : 'No';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->is_enabled;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->is_enabled;
    }
}
