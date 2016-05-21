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
     * @var PlayerModel
     */
    private $playerModel;

    public function __construct(AIService $ai, PlayerModel $playerModel)
    {
        $this->ai = $ai;
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
        /** for test purposes only */
        foreach ($game->getBattlefields() as $battlefield) {
            if (PlayerModel::isAIControlled($battlefield->getPlayer())) {
                $cells[] = $battlefield->getCellByCoordinate('B2')->addFlag(CellModel::FLAG_SHIP);
            }
        }

        /** ********************** */

        return $cells;
    }

    public function buildGame(string $json) : Game
    {
        $game = new Game();

        foreach (json_decode($json) as $playerData) {
            $player = $this->playerModel->createOnRequest($playerData->name, $playerData->flags);

            $battlefield = $this->buildBattlefield($playerData, PlayerModel::isAIControlled($player));
            $battlefield->setPlayer($player);
            $game->addBattlefield($battlefield);

            /** for test purposes only - mark player cell as damaged */
            if (!PlayerModel::isAIControlled($player)) {
                $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD_SHIP);
            }
            /** **************************************************** */
        }

        return $game;
    }

    protected function buildBattlefield(\stdClass $data, bool $defaultStateOnly = false) : Battlefield
    {
        $battlefield = new Battlefield();

        foreach ($data->cells as $cellData) {
            $flag = $defaultStateOnly || CellModel::FLAG_NONE === $cellData->flags
                ? CellModel::FLAG_NONE
                : CellModel::FLAG_SHIP;

            $cell = (new Cell())
                ->setCoordinate($cellData->coordinate)
                ->setFlags($flag);
            $battlefield->addCell($cell);
        }

        return $battlefield;
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
            $response->setResult($game->getResult());

            return $response;
        }

        foreach ($game->getBattlefields() as $playerBattlefield) {
            $player = $playerBattlefield->getPlayer();

            foreach ($game->getBattlefields() as $battlefield) {
                if ($playerBattlefield === $battlefield) {
                    /** do not process player's turn on own battlefield */
                    continue;
                }

                $_cell = $this->processPlayerTurn($player, $battlefield, $cell);
                /** to mark cells around dead ship as skipped */
                CellModel::isShipDead($_cell);

                if (!BattlefieldModel::hasUnfinishedShips($battlefield)) {
                    $result = (new GameResult())
                        ->setPlayer($player);
                    $game->setResult($result);
                    $response->setResult($result);

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
            : CellModel::switchPhase($cell);
    }
}
