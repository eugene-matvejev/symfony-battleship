<?php

namespace EM\Tests\PHPUnit\GameBundle\Entity;

use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\MockFactory\Entity\CellMockTrait;

/**
 * @see AbstractFlaggedEntity
 */
class AbstractFlaggedEntityTest extends \PHPUnit_Framework_TestCase
{
    use CellMockTrait;

    /**
     * @see AbstractFlaggedEntity::addMask
     * @test
     */
    public function addFlag()
    {
        $cell = $this->getCellMock('A1');

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
        $cell = $this->getCellMock('A1');
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
        $cell = $this->getCellMock('A1');
        $cell->setFlags(CellModel::FLAG_DEAD_SHIP);

        $this->assertTrue($cell->hasFlag(CellModel::FLAG_DEAD_SHIP));
        $this->assertTrue($cell->hasFlag(CellModel::FLAG_DEAD));
        $this->assertTrue($cell->hasFlag(CellModel::FLAG_SHIP));

        $cell->removeFlag(CellModel::FLAG_DEAD);
        $this->assertFalse($cell->hasFlag(CellModel::FLAG_DEAD_SHIP));
        $this->assertFalse($cell->hasFlag(CellModel::FLAG_DEAD));
    }
}
