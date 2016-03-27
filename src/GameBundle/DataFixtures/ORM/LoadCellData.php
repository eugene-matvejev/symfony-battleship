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
        foreach (CellModel::STATES_ALL as $state) {
            $om->persist((new CellState())->setId($state));
        }

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
