<?php

namespace Smart\CoreBundle\Composer;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as SymfonyScriptHandler;

class ScriptHandler extends SymfonyScriptHandler
{
    /**
     * @param $event Event A instance
     */
    public static function doctrineSchemaCheck(Event $event)
    {
        $options = parent::getOptions($event);
        $appDir = $options['symfony-bin-dir'];

        if (null === $appDir) {
            return;
        }

        try {
            static::executeCommand($event, $appDir, 'doctrine:schema:update', $options['process-timeout']);
            static::executeCommand($event, $appDir, 'doctrine:schema:validate', $options['process-timeout']);
        } catch (\RuntimeException $e) {
            // do nothing
        }
    }
}
