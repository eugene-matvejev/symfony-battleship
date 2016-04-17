<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;

/**
 * @since 3.0
 */
class AIService
{
    /**
     * @var CellModel
     */
    private $cellModel;
    /**
     * @var AIStrategyService
     */
    private $strategyService;

    public function __construct(CellModel $model, AIStrategyService $service)
    {
        $this->cellModel = $model;
        $this->strategyService = $service;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell
     * @throws AIException
     */
    public function processCPUTurn(Battlefield $battlefield) : Cell
    {
        $cells = $this->strategyService->chooseCells($battlefield);

        if (null === $cell = $this->pickCellToAttack($cells)) {
            $cells = BattlefieldModel::getLiveCells($battlefield);
            $cell = $this->pickCellToAttack($cells);
        }

        return $cell;
    }

    /**
     * @param Cell $cell
     *
     * @return Cell
     * @throws AIException
     */
    private function attackCell(Cell $cell) : Cell
    {
        if ($cell->hasMask(CellModel::MASK_DEAD)) {
            throw new AIException("cell: {$cell->getId()} have wrong phase: {$cell->getMask()}");
        }

        return $this->cellModel->switchPhase($cell);
    }

    /**
     * @param Cell[] $cells
     *
     * @return Cell|null
     * @throws AIException
     */
    private function pickCellToAttack(array $cells)
    {
        return empty($cells) ? null : $this->attackCell($cells[array_rand($cells, 1)]);
    }
}
