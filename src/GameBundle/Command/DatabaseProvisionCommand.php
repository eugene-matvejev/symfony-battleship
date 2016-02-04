<?php

namespace EM\GameBundle\Command;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;
use Composer\Script\CommandEvent;

/**
 * @since 2.0
 */
class DatabaseProvisionCommand extends ScriptHandler
{
    static public function initDatabases(CommandEvent $event)
    {
        if (null !== $consoleDir = static::getConsoleDir($event, 'install assets')) {
            static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=prod --if-not-exists');

            if ($event->isDevMode()) {
                static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=test --if-not-exists');
            }
        }
    }
}