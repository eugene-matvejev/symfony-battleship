<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\CellMockTrait;

/**
 * @see AIService
 */
class AIServiceTest extends ExtendedTestSuite
{
    use CellMockTrait;
    /**
     * @var AIService
     */
    protected $ai;

    protected function setUp()
    {
        parent::setUp();
        $this->ai = $this->getContainer()->get('battleship.game.services.ai.core.service');
    }

    /**
     * @see AIService::chooseCellToAttack
     * @test
     */
    public function chooseCellToAttack()
    {
        $this->assertNull($this->invokePrivateMethod(AIService::class, $this->ai, 'chooseCellToAttack', ['cells' => []]));
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCell()
    {
        $cellToException = array_merge(CellModel::STATES_DIED, [CellModel::STATE_WATER_SKIP]);

        foreach (CellModel::STATES_ALL as $cellStateId) {
            try {
                $cell = $this->getCellMock($cellStateId);
                $this->invokePrivateMethod(AIService::class, $this->ai, 'attackCell', [$cell]);
                $this->assertContains($cell->getState()->getId(), CellModel::STATES_DIED);
            } catch (AIException $e) {
                $this->assertContains($cell->getState()->getId(), $cellToException);
            }
        }
    }
}
