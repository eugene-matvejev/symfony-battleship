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
        $playerCPU = (new Player())
            ->setName('CPU')
            ->setMask(PlayerModel::FLAG_AI_CONTROLLED);
        $playerHuman = (new Player())
            ->setName('Human')
            ->setMask(PlayerModel::FLAG_NONE);
        $om->persist($playerCPU);
        $om->persist($playerHuman);

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
