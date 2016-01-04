<?php

namespace GameBundle\Library\AI;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Entity\Player;
use GameBundle\Library\Exception\AIException;
use GameBundle\Library\Exception\BattlefieldException;
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
     * @var AIStrategy
     */
    private $strategyService;
    /**
     * @var bool[]
     */
    private $cpuTurnsPerPlayer;

    /**
     * @param CellModel  $model
     * @param AIStrategy $service
     * @param Logger     $logger
     */
    public function __construct(CellModel $model, AIStrategy $service, Logger $logger)
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
    public function isTurnDoneForPlayer(Player $player) : bool
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
        $this->logger->addDebug('---------------------------------------------- BF: '. $battlefield->getId());

        $cells = $this->strategyService->chooseStrategy($battlefield);
        $log = [];

        foreach($cells as $cell) {
            $log[] = CellModel::getJSON($cell);
        }

        $this->logger->addDebug(__CLASS__ .':'. __FUNCTION__ . ' :: cells: '. print_r($log, true));

        try {
            $cell = $this->bombardInRange($cells);
            if(null === $cell) {
                $cells = BattlefieldModel::getLiveCells($battlefield);
                return $this->bombardInRange($cells);
            }
        } catch(AIException $e) {
            $this->logger->addDebug(__CLASS__ .':'. __FUNCTION__ . $e);
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
        $count = count($cells);
        $arr = [];
        foreach($cells as $el) {
            $arr[] = $el;
        }

        return 0 !== $count ? $this->bombard($arr[rand(0, $count - 1)]) : null;
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