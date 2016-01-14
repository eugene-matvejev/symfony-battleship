<?php

namespace GameBundle\Command;

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
     */
    protected function configure()
    {
        $this
            ->setName('battleship:database:seed')
            ->setDescription('Improved load doctrine fixtures with ignoring foreign keys');
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
//        $cmd = $this->getApplication()->find('doctrine:database:drop');
        $opt = [
            'command' => 'doctrine:database:drop',
//            '--env' => 'test',
            '--force' => true,
            '--if-exists' => true,
            '--verbose' => true,
            '--no-interaction' => true
        ];
//        $cmd->run(new ArrayInput($opt), $output);
        $this->getApplication()->run(new ArrayInput($opt));

        /** init fresh database */
//        $cmd = $this->getApplication()->find('doctrine:database:create');
        $opt = [
            'command' => 'doctrine:database:create',
//            '--env' => 'test',
            '--verbose' => true,
            '--no-interaction' => true
        ];
//        $cmd->run(new ArrayInput($opt), $output);
        $this->getApplication()->run(new ArrayInput($opt));

        /** populate database with mandatory data */
        $cmd = $this->getApplication()->find('doctrine:migrations:migrate');
        $opt = [
            'command' => 'doctrine:migrations:migrate',
//            '--env' => 'test',
            '--verbose' => true,
            '--no-interaction' => true
        ];
//        $cmd->execute(new ArrayInput($opt), $output);
        $this->getApplication()->run(new ArrayInput($opt));
        /** seed database with optional data */
//        $cmd = $this->getApplication()->find('doctrine:fixtures:load');
        $opt = [
            'command' => 'doctrine:fixtures:load',
//            '--env' => 'test',
            '--append' => true,
            '--verbose' => true,
            '--no-interaction' => true
        ];
//        $cmd->execute(new ArrayInput($opt), $output);
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