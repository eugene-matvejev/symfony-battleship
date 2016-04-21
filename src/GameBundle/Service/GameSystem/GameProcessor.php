<?php

namespace EM\GameBundle\Service\GameSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Entity\Player;
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
            if (PlayerModel::isAIControlled($battlefield->getPlayer())) {
                $cells[] = $battlefield->getCellByCoordinate('B2')->addMask(CellModel::MASK_SHIP);
            }
        }

        return $cells;
    }

    public function processGameInitiation(string $json) : Game
    {
        $game = new Game();

        foreach (json_decode($json)->data as $data) {
            $shouldBeControllerByAI = (($data->player->type ?? PlayerModel::MASK_NONE) & PlayerModel::MASK_AI_CONTROLLED) === PlayerModel::MASK_AI_CONTROLLED;

            $player = $this->playerModel->createOnRequest($data->player->name, $shouldBeControllerByAI);
            $battlefield = (new Battlefield())
                ->setGame($game)
                ->setPlayer($player);
            $game->addBattlefield($battlefield);

            foreach ($data->cells as $_cell) {
                $mask = PlayerModel::isAIControlled($player)
                    ? CellModel::MASK_NONE
                    : (0 !== $_cell->state ? CellModel::MASK_SHIP : CellModel::MASK_NONE);

                $cell = (new Cell())
                    ->setCoordinate($_cell->coordinate)
                    ->addMask($mask);
                $battlefield->addCell($cell);
            }

            if (!PlayerModel::isAIControlled($player)) {
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

        foreach ($game->getBattlefields() as $playerBattlefield) {
            $player = $playerBattlefield->getPlayer();

            foreach ($game->getBattlefields() as $battlefield) {
                if ($playerBattlefield === $battlefield) {
                    continue;
                }

                $_cell = $this->processPlayerTurn($player, $battlefield, $cell);
                $this->cellModel->isShipDead($_cell);

                if (!BattlefieldModel::hasUnfinishedShips($battlefield)) {
                    $result = (new GameResult())
                        ->setPlayer($player);
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
     * @param Player      $player
     * @param Battlefield $battlefield
     * @param Cell        $cell
     *
     * @return Cell
     */
    private function processPlayerTurn(Player $player, Battlefield $battlefield, Cell $cell) : Cell
    {
        return PlayerModel::isAIControlled($player)
            ? $this->ai->processCPUTurn($battlefield)
            : $this->cellModel->switchPhase($cell);
    }
}
