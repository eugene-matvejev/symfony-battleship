<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;

/**
 * @see   AIServiceTest
 *
 * @since 3.0
 */
class AIService
{
    /**
     * @var AIStrategyService
     */
    private $strategyService;

    public function __construct(AIStrategyService $service)
    {
        $this->strategyService = $service;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell
     * @throws AIException
     * @throws CellException
     */
    public function processCPUTurn(Battlefield $battlefield) : Cell
    {
        $cells = $this->strategyService->chooseCells($battlefield);

        try {
            return self::pickCellToAttack($cells);
        } catch (CellException $e) {
            $cells = BattlefieldModel::getLiveCells($battlefield);

            return self::pickCellToAttack($cells);
        }
    }

    /**
     * @param Cell[] $cells
     *
     * @return Cell
     * @throws AIException
     * @throws CellException
     */
    private static function pickCellToAttack(array $cells) : Cell
    {
        if (empty($cells)) {
            throw new CellException('no cells provided');
        }

        return self::attackCell($cells[array_rand($cells, 1)]);
    }

    /**
     * @param Cell $cell
     *
     * @return Cell
     * @throws AIException
     */
    private static function attackCell(Cell $cell) : Cell
    {
        if ($cell->hasFlag(CellModel::FLAG_DEAD)) {
            throw new AIException("cell: {$cell->getId()} already flagged as *DEAD*");
        }

        return CellModel::switchPhase($cell);
    }
}
