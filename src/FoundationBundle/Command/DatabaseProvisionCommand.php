<?php

namespace EM\FoundationBundle\Command;

use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

/**
 * @since 3.5
 */
class DatabaseProvisionCommand extends ScriptHandler
{
    public static function initDatabases(CommandEvent $event)
    {
        if (null !== $consoleDir = static::getConsoleDir($event, 'install assets')) {
            static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=prod --if-not-exists');
            static::executeCommand($event, $consoleDir, 'doctrine:migrations:migrate --env=prod --no-interaction');

            if ($event->isDevMode()) {
                static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=test --if-not-exists');
            }
        }
    }
}
