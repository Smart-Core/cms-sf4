<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Composer;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as SymfonyScriptHandler;

//"Monolith\\CMSBundle\\Composer\\ScriptHandler::createThemesSymlink"

class ScriptHandler extends SymfonyScriptHandler
{
    /**
     * @param $event Event A instance
     */
    public static function createThemesSymlink(Event $event)
    {
        $options = parent::getOptions($event);
        $appDir = $options['symfony-bin-dir'];

        if (null === $appDir) {
            return;
        }

        try {
            static::executeCommand($event, $appDir, 'cms:themes:create-symlinks', $options['process-timeout']);
        } catch (\RuntimeException $e) {
            // do nothing
        }
    }
}
