<?php

namespace GameBundle\Library\AI;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Entity\Player;
use GameBundle\Library\AI\Coordinate\CoordinateStrategy;
use GameBundle\Library\AI\Exception\AIException;
use GameBundle\Model\BattlefieldModel;
use GameBundle\Model\CellModel;
use Symfony\Bridge\Monolog\Logger;

class AI
{
    /**
     * @var CellModel
     */
    private $cellModel;
    /**
     * @var CoordinateStrategy
     */
    private $strategyService;
    /**
     * @var bool[]
     */
    private $cpuTurnsPerPlayer;

    /**
     * @param CellModel          $model
     * @param CoordinateStrategy $service
     * @param Logger             $logger
     */
    public function __construct(CellModel $model, CoordinateStrategy $service, Logger $logger)
    {
        $this->cellModel = $model;
        $this->strategyService = $service;
        $this->cpuTurnsPerPlayer = [];
        $this->logger = $logger;
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function isTurnDoneForPlayer(Player $player) : \bool
    {
        return isset($this->cpuTurnsPerPlayer[$player->getId()]);
    }

    /**
     * @param Player $player
     *
     * @return $this
     */
    public function setTurnDoneForPlayer(Player $player)
    {
        $this->cpuTurnsPerPlayer[$player->getId()] = true;

        return $this;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell
     * @throws AIException
     */
    public function turn(Battlefield $battlefield) : Cell
    {
        $this->logger->addDebug('----------------------------------------------');

        $cells = $this->strategyService->findPair($battlefield);
        $log = [];

        foreach($cells as $cell) {
            $log[] = CellModel::getJSON($cell);
        }

        $this->logger->addDebug(__FUNCTION__ . ' :: '. print_r($log, true));

        if(null === $cell = $this->bombardInRange($cells)) {
            $cells = BattlefieldModel::getLiveCells($battlefield);
            if(null === $cell = $this->bombardInRange($cells)) {
                throw new AIException('Unable to hit any cell');
            }
        }

        $this->logger->addDebug('----------------------------------------------');

        return $cell;
    }


    /**
     * @param Cell[] $cells
     *
     * @return Cell
     */
    private function bombardInRange(array $cells)
    {
        $count = count($cells);
        if(0 !== $count) {
            /** because starts from 0 */
            $rand = rand(0, $count - 1);
            $cell = $cells[$rand];

            return $this->bombard($cell);
        }

        return null;
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
            $this->setTurnDoneForPlayer($cell->getBattlefield()->getPlayer());

            return $cell;
        }

        throw new AIException(__CLASS__ .':'. __FUNCTION__ .' cell: '. $cell->getId() .' have wrong state: '. $cell->getState()->getName());
    }
}