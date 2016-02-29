<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\Strategy\AbstractStrategy;
use EM\GameBundle\Service\AI\Strategy\RandomStrategy;
use EM\GameBundle\Service\CoordinateSystem\CoordinatesPair;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see AbstractStrategy
 */
class AbstractStrategyTest extends ExtendedTestCase
{
    const COORDINATE_X = 2;
    const COORDINATE_Y = 2;

    /**
     * @var CellModel
     */
    protected $cellModel;
    /**
     * @var RandomStrategy
     */
    protected $strategyService;

    protected function setUp()
    {
        parent::setUp();
        $this->cellModel = $this->getContainer()->get('battleship.game.services.cell.model');
        $this->strategyService = $this->getContainer()->get('battleship.game.services.ai.rand.strategy.service');
    }

    /**
     * @see AbstractStrategy::verifyByCoordinates
     * @test
     */
    public function verifyByCoordinates()
    {
        $coordinatesPairs = [
            new CoordinatesPair(CoordinatesPair::WAY_UP, 1, 2),
            new CoordinatesPair(CoordinatesPair::WAY_RIGHT, 0, 1),
            new CoordinatesPair(CoordinatesPair::WAY_DOWN, 1, 0),
            new CoordinatesPair(CoordinatesPair::WAY_LEFT, 2, 1)
        ];

        $this->strategyService->getCellModel()->indexCells($this->getMockedBattlefield());
        $cells = $this->invokePrivateMethod(RandomStrategy::class, $this->strategyService, 'verifyByCoordinates', [$coordinatesPairs]);

        $this->assertCount(4, $cells);

        $cellState = (new CellState())
            ->setName('test cell state')
            ->setId(CellModel::STATE_WATER_DIED);
        foreach($coordinatesPairs as $coordinatesPair) {
            $this->cellModel->getByCoordinatesPair($coordinatesPair)->setState($cellState);
        }

        $cells = $this->invokePrivateMethod(RandomStrategy::class, $this->strategyService, 'verifyByCoordinates', [$coordinatesPairs]);
        $this->assertEmpty($cells);
    }

    /**
     * @coversNothing
     */
    protected function getMockedBattlefield() : Battlefield
    {
        $battlefield = new Battlefield();
        $cellState = (new CellState())
            ->setName('test cell state')
            ->setId(CellModel::STATE_WATER_LIVE);
        for($x = 0; $x < 10; $x++) {
            for($y = 0; $y < 10; $y++) {
                $cell = (new Cell())
                    ->setX($x)
                    ->setY($y)
                    ->setState($cellState);

                $battlefield->addCell($cell);
            }
        }

        return $battlefield;
    }

    /**
     * @param int $x
     * @param int $y
     *
     * @return Cell
     *
     * @coversNothing
     */
    protected function getMockedCell(int $x = self::COORDINATE_X, int $y = self::COORDINATE_Y) : Cell
    {
        $cellState = (new CellState())
            ->setName('test cell state')
            ->setId(CellModel::STATE_WATER_LIVE);
        $cell = (new Cell())
            ->setX($x)
            ->setY($y)
            ->setState($cellState);
        return $cell;
    }
}
