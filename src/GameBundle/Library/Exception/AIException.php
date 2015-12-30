<?php

namespace GameBundle\Library\AI\Exception;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Entity\Player;
use GameBundle\Library\AI\Coordinate\CoordinateStrategy;
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
        return null !== $this->cpuTurnsPerPlayer[$player->getId()];
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

        $cells = $this->unfinishedShips($battlefield);

        $log = [];
        $count = count($cells);
        foreach($cells as $cell) {
            $log[] = CellModel::getJSON($cell);
        }

        $this->logger->addDebug(__FUNCTION__ . ' :: '. $count .' :: '. print_r($log, true));

        $this->bombardInRange($cells);

        $cell = $this->keepBombard($battlefield);
        if(!$cell instanceof Cell) {
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
//            $cell = $this->tryWaters($battlefield);
        }

        $this->logger->addDebug('----------------------------------------------');

        return $cell;
    }

//    /**
//     * @param Battlefield $battlefield
//     *
//     * @return Cell
//     */
//    private function keepBombard(Battlefield $battlefield)
//    {
//        $cells = $this->unfinishedShips($battlefield);
//
//        $log = [];
//        $count = count($cells);
//        foreach($cells as $cell) {
//            $log[] = CellModel::getJSON($cell);
//        }
//
//        $this->logger->addDebug(__FUNCTION__ . ' :: '. $count .' :: '. print_r($log, true));
//
//        return $this->bombardInRange($cells);
//    }

//    /**
//     * @param Battlefield $battlefield
//     *
//     * @return Cell
//     */
//    private function tryWaters(Battlefield $battlefield)
//    {
//        $cells = [];
//        $log = [];
//        foreach($battlefield->getCells() as $cell) {
//            if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
//                $cells[] = $cell;
//                $log[] = CellModel::getJSON($cell);
//            }
//        }
//        $count = count($cells);
//
//        $this->logger->addDebug(__FUNCTION__ . ' :: '. $count .' :: '. print_r($log, true));
//
//        return $this->bombardInRange($cells);
//    }

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
//        $size = BattlefieldModel::getSize($battlefield);
        $strategy = new CoordinateStrategy(1, 4);
        $coordinates = $strategy->findPair($battlefield);
        $log = [];

        foreach($coordinates as $coordinate) {
            $log[] = CellModel::getJSON($coordinate);
        }

        $this->logger->addDebug(__FUNCTION__ . ' :: '. print_r($log, true));

        return $coordinates;
    }


}