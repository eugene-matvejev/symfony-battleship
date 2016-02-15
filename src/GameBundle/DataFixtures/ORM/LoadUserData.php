<?php

namespace EM\GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 3.5
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $playerTypeCPU = (new PlayerType())
            ->setId(PlayerModel::TYPE_CPU)
            ->setName('Player controlled by computer');
        $playerTypeHuman = (new PlayerType())
            ->setId(PlayerModel::TYPE_HUMAN)
            ->setName('Player controlled by human');
        $om->persist($playerTypeCPU);
        $om->persist($playerTypeHuman);

        $playerCPU = (new Player())
            ->setName('TEST CPU')
            ->setType($playerTypeHuman);
        $playerHuman = (new Player())
            ->setName('TEST PLAYER')
            ->setType($playerTypeHuman);
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
