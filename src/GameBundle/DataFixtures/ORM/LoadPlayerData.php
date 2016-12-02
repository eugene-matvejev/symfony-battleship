<?php

namespace EM\GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @since 3.5
 */
class LoadPlayerData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    const TEST_PLAYER_EMAIL    = 'test.user@example.com';
    const TEST_PLAYER_PASSWORD = 'test.user.password';
    const TEST_AI_PLAYER_EMAIL = 'CPU 0';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $model = $this->container->get('battleship_game.service.player_model');

        $humanPlayer = $model->createPlayer(static::TEST_PLAYER_EMAIL, static::TEST_PLAYER_PASSWORD);
        $om->persist($humanPlayer);
        $aiPlayer = $model->createOnRequestAIControlled(static::TEST_AI_PLAYER_EMAIL);
        $om->persist($aiPlayer);

        $om->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
