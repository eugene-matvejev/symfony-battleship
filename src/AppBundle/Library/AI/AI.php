<?php

namespace AppBundle\Library\AI;

use AppBundle\Entity\Battlefield;
use AppBundle\Entity\Cell;
use AppBundle\Entity\Player;
use AppBundle\Model\BattlefieldModel;
use AppBundle\Model\CellModel;
use Symfony\Bridge\Monolog\Logger;

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
     * @param CellModel $model
     * @param Logger $logger
     */
    public function __construct(CellModel $model, Logger $logger)
    {
        $this->cellModel = $model;
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
     * @param Battlefield $battlefield
     *
     * @return Cell
     */
    public function turn(Battlefield $battlefield) : Cell
    {
        $this->logger->addDebug('----------------------------------------------');

        $cell = $this->keepBombard($battlefield);
        if(!$cell instanceof Cell) {
            $cell = $this->tryWaters($battlefield);
        }

        $this->logger->addDebug('----------------------------------------------');

        return $cell;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell
     */
    private function keepBombard(Battlefield $battlefield)
    {
        $cells = $this->unfinishedShips($battlefield);

        $log = [];
        $count = count($cells);
        foreach($cells as $cell) {
            $log[] = CellModel::getJSON($cell);
        }

        $this->logger->addDebug(__FUNCTION__ . ' :: '. $count .' :: '. print_r($log, true));

        return $this->bombardInRange($cells);
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell
     */
    private function tryWaters(Battlefield $battlefield)
    {
        $cells = [];
        $log = [];
        foreach($battlefield->getCells() as $cell) {
            if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
                $cells[] = $cell;
                $log[] = CellModel::getJSON($cell);
            }
        }
        $count = count($cells);

        $this->logger->addDebug(__FUNCTION__ . ' :: '. $count .' :: '. print_r($log, true));

        return $this->bombardInRange($cells);
    }

    /**
     * @param Cell[] $cells
     *
     * @return Cell
     */
    private function bombardInRange(array $cells)
    {
        $count = count($cells);
        if($count !== 0) {
            /** because starts from 0 */
//            0-2
            $rand = rand(0, $count - 1);
            $cell = $cells[$rand];

            return $this->bombard($cell);
        }
    }

    /**
     * @param Cell $cell
     *
     * @return Cell
     */
    private function bombard(Cell $cell)
    {
        if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
            $this->cellModel->switchState($cell);
            $this->setTurnDoneForPlayer($cell->getBattlefield()->getPlayer());

            return $cell;
        }
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    private function unfinishedShips(Battlefield $battlefield) : array
    {
        $size = BattlefieldModel::getSize($battlefield);

        foreach($battlefield->getCells() as $cell) {
            if($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                continue;
            }

            $coordinates = [];
            if($cell->getX() !== 0)
                $coordinates[] = ['x' => $cell->getX() - 1, 'y' => $cell->getY()];
            if($cell->getX() !== $size - 1);
                $coordinates[] = ['x' => $cell->getX() + 1, 'y' => $cell->getY()];
            if($cell->getY() !== 0)
                $coordinates[] = ['x' => $cell->getX(), 'y' => $cell->getY() - 1];
            if($cell->getY() !== $size - 1)
                $coordinates[] = ['x' => $cell->getX(), 'y' => $cell->getY() + 1];

            $return = $log = [];

            foreach($coordinates as $coordinate) {
                $_cell = BattlefieldModel::getCellByCoordinates($battlefield, $coordinate['x'], $coordinate['y']);
                if(in_array($_cell->getState()->getId(), CellModel::getLiveStates())) {
                    $return[] = $_cell;
                    $log[] = CellModel::getJSON($_cell);
                }
            }

            if(count($return) !== 0) {
                $this->logger->addDebug(__FUNCTION__ . ' :: '. print_r($log, true));

                return $return;
            }
        }

        return [];
    }
}