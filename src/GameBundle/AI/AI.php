<?php

namespace EM\GameBundle\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;

/**
 * @since 3.0
 */
class AI
{
    /**
     * @var CellModel
     */
    private $cellModel;
    /**
     * @var AIStrategy
     */
    private $strategyService;

    public function __construct(CellModel $model, AIStrategy $service)
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
    public function turn(Battlefield $battlefield) : Cell
    {
        try {
            $cells = $this->strategyService->chooseStrategy($battlefield);

            if(null === $cell = $this->chooseCellToAttack($cells)) {
                $cells = BattlefieldModel::getLiveCells($battlefield);
                $cell = $this->chooseCellToAttack($cells);
            }

            return $cell;
        } catch(AIException $e) {
        }
    }

    /**
     * @param Cell[] $cells
     *
     * @return Cell|null
     * @throws AIException
     */
    private function chooseCellToAttack(array $cells)
    {
        return empty($cells) ? null : $this->attackCell($cells[array_rand($cells, 1)]);
    }

    /**
     * @param Cell $cell
     *
     * @return Cell
     * @throws AIException
     */
    private function attackCell(Cell $cell) : Cell
    {
        if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
            $this->cellModel->switchState($cell);

            return $cell;
        }

        throw new AIException(__CLASS__ .':'. __FUNCTION__ .' cell: '. $cell->getId() .' have wrong state: '. $cell->getState()->getName());
    }
}