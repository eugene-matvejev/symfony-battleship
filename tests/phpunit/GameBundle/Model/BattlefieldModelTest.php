<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see BattlefieldModel
 */
class BattlefieldModelTest extends ExtendedTestCase
{
    /**
     * @see BattlefieldModel::getLiveCells
     * @test
     */
    public function getLiveCells()
    {
        $battlefield = $this->getMockedBattlefield();
        $cells = BattlefieldModel::getLiveCells($battlefield);
        $this->assertCount(100, $cells);

        $cellState = (new CellState())
            ->setId(CellModel::STATE_SHIP_DIED);
        foreach ($battlefield->getCells() as $cell) {
            if ($cell->getX() === 0) {
                $cell->setState($cellState);
            }
        }
        $cells = BattlefieldModel::getLiveCells($battlefield);
        $this->assertCount(90, $cells);
    }

    /**
     * @see BattlefieldModel::isUnfinished
     * @test
     */
    public function isUnfinished()
    {
        $battlefield = $this->getMockedBattlefield();
        $this->assertFalse(BattlefieldModel::isUnfinished($battlefield));

        $cellState = (new CellState())
            ->setId(CellModel::STATE_SHIP_DIED);
        foreach ($battlefield->getCells() as $cell) {
            $cell->setState($cellState);
        }
        $this->assertTrue(BattlefieldModel::isUnfinished($battlefield));
    }

    /**
     * @coversNothing
     */
    private function getMockedBattlefield()
    {
        $battlefield = new Battlefield();
        $cellState = (new CellState())
            ->setId(CellModel::STATE_SHIP_LIVE);
        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                $cell = (new Cell())
                    ->setX($x)
                    ->setY($y)
                    ->setState($cellState);
                $battlefield->addCell($cell);
            }
        }

        return $battlefield;
    }
}
