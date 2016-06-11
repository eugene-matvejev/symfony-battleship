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
use EM\GameBundle\Request\GameInitiationRequest;
use EM\GameBundle\Response\GameTurnResponse;
use EM\GameBundle\Service\AI\AIService;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

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

    private function attachAIBattlefields(Game $game, int $amount, int $size)
    {
        for ($i = 0; $i < $amount; $i++) {
            $player = $this->playerModel->createOnRequestAIControlled("CPU {$i}");

            /** hard-code ship into B2 for testing purposes */
            $battlefield = BattlefieldModel::generate($size, ['B2'])
                ->setPlayer($player);
            $game->addBattlefield($battlefield);
        }
    }

    public function buildGame(GameInitiationRequest $request) : Game
    {
        $game = new Game();
        $this->attachAIBattlefields($game, $request->getOpponents(), $request->getSize());

        $player = $this->playerModel->createOnRequestHumanControlled($request->getPlayerName());

        $battlefield = BattlefieldModel::generate($request->getSize(), $request->getCoordinates());
        $battlefield->setPlayer($player);
        $game->addBattlefield($battlefield);

        /** for test purposes only - mark player cell as damaged */
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD_SHIP);

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

                if (CellModel::isShipDead($_cell)) {

                    $processor = new PathProcessor($_cell->getCoordinate());
                    $cells = $processor->getAdjacentCells($_cell->getBattlefield(), 4, CellModel::FLAG_SHIP);
                    $cells[$_cell->getCoordinate()] = $_cell;
                    ksort($cells);

                    $_cells = [];
                    foreach ($cells as $shipCell) {
                        $__cells = $processor->reset($shipCell->getCoordinate())->getAdjacentCells($battlefield, 1, 0, CellModel::FLAG_SHIP);
                        $_cells = array_merge(
                            $_cells,
                            $__cells
                        );

                        foreach ($_cells as $waterCell) {
                            CellModel::switchPhase($waterCell, CellModel::FLAG_SKIP);
                        }
                    }
                }

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
