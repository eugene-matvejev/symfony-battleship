<?php

namespace EM\GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Request\GameInitiationRequest;
use EM\Tests\Environment\AbstractKernelTestSuite;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Finder\Finder;

/**
 * @since 23.0
 */
class LoadGameData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $directory = AbstractKernelTestSuite::getSharedFixturesDirectory();

        $finder = new Finder();
        $finder->files()->in("{$directory}/game-initiation-requests/valid");

        $builder = $this->container->get('battleship_game.service.game_builder');
        $player  = $om->getRepository(Player::class)->findOneBy(['email' => LoadPlayerData::TEST_PLAYER_EMAIL]);

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
