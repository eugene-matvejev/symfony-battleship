<?php

namespace EM\GameBundle\Service\GameSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
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

    private function initCPUBattlefield(Battlefield $battlefield)
    {
        $liveShipState = $this->cellModel->getAllStates()[CellModel::STATE_SHIP_LIVE];

        $battlefield->getCellByCoordinate('B2')->setState($liveShipState);
    }

    public function processGameInitiation(string $json) : Game
    {
        $game = new Game();

        foreach (json_decode($json)->data as $data) {
            $battlefield = (new Battlefield())
                ->setGame($game)
                ->setPlayer($this->playerModel->createOnRequest($data->player->name));
            $game->addBattlefield($battlefield);

            foreach ($data->cells as $_cell) {
                $cell = (new Cell())
                    ->setCoordinate($_cell->coordinate)
                    ->setState($battlefield->getPlayer()->getType()->getId() !== PlayerModel::TYPE_CPU
                        ? $this->cellModel->getAllStates()[$_cell->state]
                        : $this->cellModel->getAllStates()[CellModel::STATE_WATER_LIVE]);
                $battlefield->addCell($cell);
            }

            if ($battlefield->getPlayer()->getType()->getId() === PlayerModel::TYPE_CPU) {
                $this->initCPUBattlefield($battlefield);
            }
        }

        return $game;
    }

    /**
     * @param Cell $cell
     *
     * @return GameTurnResponse
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
            $_cell = $this->processPlayerTurn($battlefield, $cell->getCoordinate());
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
     * @param string      $playerCellCoordinate
     *
     * @return Cell
     * @throws PlayerException
     */
    private function processPlayerTurn(Battlefield $battlefield, string $playerCellCoordinate) : Cell
    {
        switch ($battlefield->getPlayer()->getType()->getId()) {
            case PlayerModel::TYPE_CPU:
                return $this->cellModel->switchState($battlefield->getCellByCoordinate($playerCellCoordinate));
            case PlayerModel::TYPE_HUMAN:
                return $this->ai->processCPUTurn($battlefield);
        }

        throw new PlayerException("player: {$battlefield->getPlayer()->getId()} has unknown type {$battlefield->getPlayer()->getType()->getId()}");
    }
}
