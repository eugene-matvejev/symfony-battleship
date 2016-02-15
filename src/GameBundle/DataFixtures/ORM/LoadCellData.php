<?php

namespace EM\GameBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\CellModel;

/**
 * @since 3.5
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
            ->setName('test shot water');
        $cellStateShipLive = (new CellState())
            ->setId(CellModel::STATE_SHIP_LIVE)
            ->setName('test live ship');
        $cellStateShipDied = (new CellState())
            ->setId(CellModel::STATE_SHIP_DIED)
            ->setName('test damaged ship');
        $cellStateWaterSkip = (new CellState())
            ->setId(CellModel::STATE_WATER_SKIP)
            ->setName('test water to');

        $om->persist($cellStateWaterLive);
        $om->persist($cellStateWaterDied);
        $om->persist($cellStateShipLive);
        $om->persist($cellStateShipDied);
        $om->persist($cellStateWaterSkip);

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
