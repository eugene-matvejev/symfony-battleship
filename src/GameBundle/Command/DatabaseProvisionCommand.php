<?php

namespace EM\GameBundle\Command;

use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

/**
 * @since 2.0
 */
class DatabaseProvisionCommand extends ScriptHandler
{
    /**
     * @param CommandEvent $event
     *
     * @return void
     */
    static public function initDatabases(CommandEvent $event)
    {
        if (null !== $consoleDir = static::getConsoleDir($event, 'install assets')) {
            static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=prod --if-not-exists');
            static::executeCommand($event, $consoleDir, 'doctrine:migrations:migrate --env=prod --no-interaction');

            if ($event->isDevMode()) {
                static::executeCommand($event, $consoleDir, 'doctrine:database:create --env=test --if-not-exists');
                static::executeCommand($event, $consoleDir, 'doctrine:migrations:migrate --env=test --no-interaction');
            }
        }
    }
}