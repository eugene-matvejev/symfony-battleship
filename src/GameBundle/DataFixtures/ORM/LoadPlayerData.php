<?php

namespace EM\GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 3.5
 */
class LoadPlayerData extends AbstractFixture implements OrderedFixtureInterface
{
    const TEST_PLAYER_EMAIL    = 'eugene.matvejev@example.com';
    const TEST_PLAYER_PASSWORD = 'password';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $model = new PlayerModel($om->getRepository('GameBundle:Player'), 'fixtures');

        $om->persist($model->createOnRequestHumanControlled(static::TEST_PLAYER_EMAIL, static::TEST_PLAYER_PASSWORD));
        $om->persist($model->createOnRequestAIControlled('cpu'));

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
