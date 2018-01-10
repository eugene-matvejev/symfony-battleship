<?php

namespace EM\Tests\PHPUnit\FoundationBundle\ORM;

use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see AbstractFlaggedEntity
 */
class AbstractFlaggedEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see AbstractFlaggedEntity::addMask
     * @test
     */
    public function addFlag()
    {
        $cell = MockFactory::getCellMock('A1');

        $cell->addFlag(CellModel::FLAG_SHIP);
        $this->assertEquals(CellModel::FLAG_SHIP, $cell->getFlags());

        $cell->addFlag(CellModel::FLAG_DEAD);
        $this->assertEquals(CellModel::FLAG_DEAD_SHIP, $cell->getFlags());
    }

    /**
     * @see AbstractFlaggedEntity::removeMask
     * @test
     */
    public function removeFlag()
    {
        $cell = MockFactory::getCellMock('A1');
        $cell->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cell->removeFlag(CellModel::FLAG_DEAD);

        $this->assertEquals(CellModel::FLAG_SHIP, $cell->getFlags());
    }

    /**
     * @see AbstractFlaggedEntity::hasMask
     * @test
     */
    public function hasFlag()
    {
        $cell = MockFactory::getCellMock('A1');
        $cell->setFlags(CellModel::FLAG_DEAD_SHIP);

        $this->assertTrue($cell->hasFlag(CellModel::FLAG_DEAD_SHIP));
        $this->assertTrue($cell->hasFlag(CellModel::FLAG_DEAD));
        $this->assertTrue($cell->hasFlag(CellModel::FLAG_SHIP));

        $cell->removeFlag(CellModel::FLAG_DEAD);
        $this->assertFalse($cell->hasFlag(CellModel::FLAG_DEAD_SHIP));
        $this->assertFalse($cell->hasFlag(CellModel::FLAG_DEAD));
    }
}
