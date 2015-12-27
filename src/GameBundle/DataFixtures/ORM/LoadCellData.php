<?php

namespace GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use GameBundle\Entity\CellState;
use GameBundle\Entity\Player;
use GameBundle\Entity\PlayerType;
use GameBundle\Model\CellModel;
use GameBundle\Model\PlayerModel;

/**
 * Class LoadLanguageData
 */
class LoadCellData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $cellStateWaterLive = (new CellState())
            ->setId(CellModel::STATE_WATER_LIVE)
            ->setName('test untouched water');
        $cellStateWaterDied = (new CellState())
            ->setId(CellModel::STATE_WATER_DIED)
            ->setName('test shooted water');
        $cellStateShipLive = (new CellState())
            ->setId(CellModel::STATE_SHIP_LIVE)
            ->setName('test live ship');
        $cellStateShipDied = (new CellState())
            ->setId(CellModel::STATE_SHIP_DIED)
            ->setName('test damaged ship');

        $om->persist($cellStateWaterLive);
        $om->persist($cellStateWaterDied);
        $om->persist($cellStateShipLive);
        $om->persist($cellStateShipDied);

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