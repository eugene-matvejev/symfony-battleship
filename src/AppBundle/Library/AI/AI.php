<?php

namespace AppBundle\Library\AI;

use AppBundle\Entity\Cell;
use AppBundle\Entity\Player;
use AppBundle\Model\CellModel;

class AI
{
    /**
     * @var CellModel
     */
    private $cellModel;

    /**
     * @var bool[]
     */
    private $cpuTurnsPerPlayer;

    /**
     *
     */
    public function __construct()
    {
        $this->cpuTurnsPerPlayer = [];
    }

    /**
     * @param CellModel $model
     *
     * @return $this
     */
    public function setCellModel(CellModel $model)
    {
        $this->cellModel = $model;

        return $this;
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function isTurnDoneForPlayer(Player $player)
    {
        return !empty($this->cpuTurnsPerPlayer[$player->getId()]);
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
     * @param Cell $cell
     *
     * @return bool
     */
    public function turn(Cell $cell)
    {
        if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
            $this->cellModel->switchState($cell);
            $this->setTurnDoneForPlayer($cell->getBattlefield()->getPlayer());
            return true;
        }

        return false;
    }
}