<?php

namespace GameBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DoctrineFixturesLoadCommand
 */
class DoctrineFixturesLoadCommand extends ContainerAwareCommand
{
    /**
     * Config method
     */
    protected function configure()
    {
        $this
            ->setName('battleship:seed:database')
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
        $doctrine = $this->getContainer()->get('doctrine');
        $om = $doctrine->getManager();

        $om->beginTransaction();
//        $conn = $doctrine->getConnection();
//        $conn->prepare('SET FOREIGN_KEY_CHECKS=0')->execute();

        $options = [
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => true,
            '--purge-with-truncate' => true,
            '--env' => 'test'
        ];

        $this->getApplication()->run(new ArrayInput($options));
//        $conn->prepare('SET FOREIGN_KEY_CHECKS=1')->execute();
        $om->commit();
    }
}