<?php

namespace EM\GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Model\PlayerSessionModel;

/**
 * @since 22.0
 */
class LoadPlayerSessionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $model = new PlayerSessionModel(
            $om->getRepository('GameBundle:PlayerSession'),
            new PlayerModel($om->getRepository('GameBundle:Player'), 'fixtures'),
            'fixtures'
        );

        $om->persist($model->authenticate('human', 'password'));

        $om->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
