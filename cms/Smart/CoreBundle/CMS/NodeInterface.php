<?php

namespace Smart\CoreBundle\CMS;

interface NodeInterface
{
    /**
     * @return array
     */
    public function getControllerParams();

    /**
     * @return array
     */
    public function getController($controllerName = null, $actionName = 'index');

}
