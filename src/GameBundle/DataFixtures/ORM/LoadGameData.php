<?php

namespace EM\GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Request\GameInitiationRequest;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * @since 22.0
 */
class LoadGameData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $finder    = new Finder();
        $directory = dirname($this->container->getParameter('kernel.root_dir'));

        $finder->files()->in("{$directory}/tests/shared-fixtures/game-initiation-requests/valid");

        $builder = $this->container->get('battleship_game.service.game_builder');
        $player  = $om->getRepository('GameBundle:Player')->findOneBy(['email' => LoadPlayerData::TEST_PLAYER_EMAIL]);

        foreach ($finder as $file) {
            for ($i = 0; $i < 3; $i++) {
                $game = $builder->buildGame(new GameInitiationRequest($file->getContents()), $player);

                $om->persist($game);
            }
        }

        $om->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
