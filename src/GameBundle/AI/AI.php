<?php

namespace EM\GameBundle\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use Symfony\Bridge\Monolog\Logger;

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
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(CellModel $model, AIStrategy $service, Logger $logger)
    {
        $this->cellModel = $model;
        $this->strategyService = $service;
        $this->logger = $logger;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell
     * @throws AIException
     */
    public function turn(Battlefield $battlefield) : Cell
    {
        $cells = $this->strategyService->chooseStrategy($battlefield);

        try {
            if(null === $cell = $cell = $this->bombardInRange($cells)) {
                $cells = BattlefieldModel::getLiveCells($battlefield);
                $cell = $this->bombardInRange($cells);
            }
        } catch(AIException $e) {
            $this->logger->addCritical(__CLASS__ .':'. __FUNCTION__ .':'. $e);
        }

        return $cell;
    }


    /**
     * @param Cell[] $cells
     *
     * @return Cell|null
     * @throws AIException
     */
    private function bombardInRange(array $cells)
    {
        return empty($cells) ? null : $this->bombard($cells[array_rand($cells, 1)]);
    }

    /**
     * @param Cell $cell
     *
     * @return Cell
     * @throws AIException
     */
    private function bombard(Cell $cell) : Cell
    {
        if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
            $this->cellModel->switchState($cell);

            return $cell;
        }

        throw new AIException(__CLASS__ .':'. __FUNCTION__ .' cell: '. $cell->getId() .' have wrong state: '. $cell->getState()->getName());
    }
}