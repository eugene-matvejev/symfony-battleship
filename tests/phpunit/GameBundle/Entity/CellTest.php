<?php

namespace EM\Tests\PHPUnit\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\MockFactory\Entity\CellMockTrait;

/**
 * @see Cell
 */
class CellTest extends \PHPUnit_Framework_TestCase
{
    use CellMockTrait;

    /**
     * @see Cell::addMask
     *
     * @test
     */
    public function addMask()
    {
        $cell = $this->getCellMock('A1');

        $cell->addMask(CellModel::MASK_SHIP);
        $this->assertEquals(CellModel::MASK_SHIP, $cell->getMask());

        $cell->addMask(CellModel::MASK_DEAD);
        $this->assertEquals(CellModel::MASK_DEAD_SHIP, $cell->getMask());
    }

    /**
     * @see Cell::removeMask
     *
     * @test
     */
    public function removeMask()
    {
        $cell = $this->getCellMock('A1');
        $cell->setMask(CellModel::MASK_DEAD_SHIP);
        $cell->removeMask(CellModel::MASK_DEAD);

        $this->assertEquals(CellModel::MASK_SHIP, $cell->getMask());
    }

    /**
     * @see Cell::hasMask
     *
     * @test
     */
    public function hasMask()
    {
        $cell = $this->getCellMock('A1');
        $cell->setMask(CellModel::MASK_DEAD_SHIP);

        $this->assertTrue($cell->hasMask(CellModel::MASK_DEAD_SHIP));
        $this->assertTrue($cell->hasMask(CellModel::MASK_DEAD));
        $this->assertTrue($cell->hasMask(CellModel::MASK_SHIP));

        $cell->removeMask(CellModel::MASK_DEAD);
        $this->assertFalse($cell->hasMask(CellModel::MASK_DEAD_SHIP));
        $this->assertFalse($cell->hasMask(CellModel::MASK_DEAD));
    }
}
