<?php

namespace EM\FoundationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @since 3.5
 */
class DatabaseSeedCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('battleship:database:seed')
            ->setDescription('seeds the database with initial data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->setAutoExit(false);
        /** drop database */
        $opt = [
            'command' => 'doctrine:database:drop',
            '--if-exists' => true,
            '--no-interaction' => true,
            '--force' => true,
            ($input->getOption('quiet') ? '--quiet' : '--verbose') => true
        ];
        $this->getApplication()->run(new ArrayInput($opt));

        /** init fresh database */
        $opt = [
            'command' => 'doctrine:database:create',
            '--if-not-exists' => true,
            '--no-interaction' => true,
            ($input->getOption('quiet') ? '--quiet' : '--verbose') => true
        ];
        $this->getApplication()->run(new ArrayInput($opt));

        /** populate database with mandatory data */
        $opt = [
            'command' => 'doctrine:migrations:migrate',
            '--no-interaction' => true,
            ($input->getOption('quiet') ? '--quiet' : '--verbose') => true

        ];
        $this->getApplication()->run(new ArrayInput($opt));

        /** seed database with optional data */
        $opt = [
            'command' => 'doctrine:fixtures:load',
            '--append' => true,
            '--no-interaction' => true,
            ($input->getOption('quiet') ? '--quiet' : '--verbose') => true
        ];
        $this->getApplication()->run(new ArrayInput($opt));
    }
}
