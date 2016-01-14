<?php

namespace EM\GameBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @since 3.1
 */
class DatabaseSeedCommand extends ContainerAwareCommand
{
    /**
     * configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('battleship:database:seed')
            ->setDescription('seeds the database with initial data');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
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

//        /** not sure what is better, for now */
//        $doctrine = $this->getContainer()->get('doctrine');
//        $om = $doctrine->getManager();
//        $om->beginTransaction();
//        $conn = $doctrine->getConnection();
//        $conn->prepare('SET FOREIGN_KEY_CHECKS=0')->execute();
//
//        $options = [
//            'command' => 'doctrine:fixtures:load',
//            '--no-interaction' => true,
//            '--purge-with-truncate' => true,
//            '--env' => 'test'
//        ];
//
//        $this->getApplication()->run(new ArrayInput($options));
//        $conn->prepare('SET FOREIGN_KEY_CHECKS=1')->execute();
//        $om->commit();
    }
}