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
class LoadPlayerTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $om->persist((new Player())
            ->setName('CPU')
            ->setFlag(PlayerModel::FLAG_AI_CONTROLLED));
        $om->persist((new Player())
            ->setName('Human')
            ->setFlag(PlayerModel::FLAG_NONE));

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
