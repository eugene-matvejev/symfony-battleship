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
class LoadPlayerTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $playerTypeCPU = (new PlayerType())
            ->setId(PlayerModel::TYPE_CPU);
        $playerTypeHuman = (new PlayerType())
            ->setId(PlayerModel::TYPE_HUMAN);
        $om->persist($playerTypeCPU);
        $om->persist($playerTypeHuman);

        $playerCPU = (new Player())
            ->setName('CPU')
            ->setType($playerTypeCPU);
        $playerHuman = (new Player())
            ->setName('Human')
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
