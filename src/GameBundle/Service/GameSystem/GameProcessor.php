<?php

namespace EM\GameBundle\Service\GameSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Exception\PlayerException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Response\GameTurnResponse;
use EM\GameBundle\Service\AI\AIService;

/**
 * @since 10.0
 */
class GameProcessor
{
    /**
     * @var AIService
     */
    private $ai;
    /**
     * @var CellModel
     */
    private $cellModel;
    /**
     * @var PlayerModel
     */
    private $playerModel;

    public function __construct(AIService $ai, CellModel $cellModel, PlayerModel $playerModel)
    {
        $this->ai = $ai;
        $this->cellModel = $cellModel;
        $this->playerModel = $playerModel;
    }

    /**
     * @param Game $game
     *
     * @return Cell[]
     */
    public function processCPUBattlefieldsInitiation(Game $game) : array
    {
        $cells = [];
        foreach ($game->getBattlefields() as $battlefield) {
            if ($this->playerModel->isCPU($battlefield->getPlayer())) {
                $cells[] = $battlefield->getCellByCoordinate('B2')->addMask(CellModel::MASK_SHIP);
            }
        }

        return $cells;
    }

    public function processGameInitiation(string $json) : Game
    {
        $game = new Game();

        foreach (json_decode($json)->data as $data) {
            $player = $this->playerModel->createOnRequest($data->player->name);
            $battlefield = (new Battlefield())
                ->setGame($game)
                ->setPlayer($player);
            $game->addBattlefield($battlefield);

            foreach ($data->cells as $_cell) {
                $mask = $this->playerModel->isCPU($player)
                    ? CellModel::MASK_NONE
                    : (0 !== $_cell->state ? CellModel::MASK_SHIP : CellModel::MASK_NONE);

                $cell = (new Cell())
                    ->setCoordinate($_cell->coordinate)
                    ->addMask($mask);
                $battlefield->addCell($cell);
            }

            if (!$this->playerModel->isCPU($player)) {
                $battlefield->getCellByCoordinate('A1')->setMask(CellModel::MASK_DEAD_SHIP);
            }
        }

        return $game;
    }

    /**
     * @param Cell $cell
     *
     * @return GameTurnResponse
     * @throws CellException
     * @throws PlayerException
     */
    public function processGameTurn(Cell $cell) : GameTurnResponse
    {
        $game = $cell->getBattlefield()->getGame();
        $response = new GameTurnResponse();

        if (null !== $game->getResult()) {
            $response->setGameResult($game->getResult());

            return $response;
        }

        foreach ($game->getBattlefields() as $battlefield) {
            $_cell = $this->processPlayerTurn($battlefield, $cell);
            $this->cellModel->isShipDead($_cell);

            if (!BattlefieldModel::hasUnfinishedShips($battlefield)) {
                foreach ($game->getBattlefields() as $_battlefield) {
                    if ($battlefield->getPlayer() === $_battlefield->getPlayer()) {
                        continue;
                    }

                    $result = (new GameResult())
                        ->setPlayer($_battlefield->getPlayer());
                    $game->setResult($result);
                    $response->setGameResult($result);

                    break 2;
                }
            }
        }

        $response->setCells(CellModel::getChangedCells());

        return $response;
    }

    /**
     * @param Battlefield $battlefield
     * @param Cell        $playerCell
     *
     * @return Cell
     * @throws CellException
     * @throws PlayerException
     */
    private function processPlayerTurn(Battlefield $battlefield, Cell $playerCell) : Cell
    {
        switch ($battlefield->getPlayer()->getType()->getId()) {
            case PlayerModel::TYPE_HUMAN:
                return $this->ai->processCPUTurn($battlefield);
            case PlayerModel::TYPE_CPU:
                if (null !== $cell = $battlefield->getCellByCoordinate($playerCell->getCoordinate())) {
                    return $this->cellModel->switchPhase($cell);
                }
                throw new CellException("cell with coordinate: {$playerCell->getCoordinate()} in battlefield: {$battlefield->getId()} doesn't exists");
        }

        throw new PlayerException("player: {$battlefield->getPlayer()->getId()} has unknown type {$battlefield->getPlayer()->getType()->getId()}");
    }
}
