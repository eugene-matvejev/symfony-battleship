<?php

namespace GameBundle\Command;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;
use Composer\Script\CommandEvent;

/**
 * @since 2.0
 */
class ComposerCommand extends ScriptHandler
{
    static public function initProductionDatabase(CommandEvent $event)
    {
        $consoleDir = static::getConsoleDir($event, 'install assets');

        if (null !== $consoleDir) {
            static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=prod --if-not-exists');

            if($event->isDevMode()) {
                static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=test --if-not-exists');
            }
        }
    }
}