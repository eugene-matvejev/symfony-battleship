<?php

namespace EM\Tests\PHPUnit\GameBundle\AI;

use EM\GameBundle\AI\AI;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see EM\GameBundle\AI\AI
 */
class AITest extends ExtendedTestCase
{
    /**
     * @var AI
     */
    protected $ai;

    protected function setUp()
    {
        parent::setUp();
        $this->ai = $this->getContainer()->get('battleship.game.services.ai.core');
    }

    /**
     * @see EM\GameBundle\AI\AI::chooseCellToAttack
     * @test
     */
    public function chooseCellToAttack()
    {
        $this->assertNull($this->invokePrivateMethod(AI::class, $this->ai, 'chooseCellToAttack', ['cells' => []]));
    }


    /**
     * @see EM\GameBundle\AI\AI::attackCell
     * @test
     */
    public function attackCell()
    {
        $cellToException = array_merge(CellModel::getDiedStates(), [CellModel::STATE_WATER_SKIP]);

        foreach (CellModel::getAllStates() as $cellStateId) {
            try {
                $cell = $this->getMockedCell($cellStateId);
                $this->invokePrivateMethod(AI::class, $this->ai, 'attackCell', [$cell]);
                $this->assertContains($cell->getState()->getId(), CellModel::getDiedStates());
            } catch (AIException $e) {
                $this->assertContains($cell->getState()->getId(), $cellToException);
            }
        }
    }

    protected function getMockedCell(int $cellStateId) : Cell
    {
        $cellState = (new CellState())
            ->setName('test cell state')
            ->setId($cellStateId);
        $cell = (new Cell())
            ->setState($cellState);

        return $cell;
    }
}