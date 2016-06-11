<?php

namespace EM\GameBundle\Service\GameSystem;

use EM\GameBundle\Entity\{
    Battlefield, Cell, Game, GameResult
};
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

        foreach ($game->getBattlefields() as $turnOwnerBattlefield) {
            foreach ($game->getBattlefields() as $targetBattlefield) {
                if ($turnOwnerBattlefield === $targetBattlefield) {
                    continue;
                }

                $_cell = $this->processPlayerTurn($targetBattlefield, $cell);

                if (CellModel::isShipDead($_cell)) {
                    $this->markWaterAroundShipSkipped($_cell);

                    if (!BattlefieldModel::hasUnfinishedShips($targetBattlefield)) {
                        $result = (new GameResult())
                            ->setPlayer($turnOwnerBattlefield->getPlayer());
                        $game->setResult($result);
                        $response->setResult($result);

                        break 2;
                    }
                }
            }
        }

        $response->setCells(CellModel::getChangedCells());

        return $response;
    }

    private function markWaterAroundShipSkipped(Cell $cell)
    {
        $processor = new PathProcessor($cell->getCoordinate());
        $battlefield = $cell->getBattlefield();

        $cells = $processor->getAdjacentCells($cell->getBattlefield(), 4, CellModel::FLAG_SHIP);
        $cells[$cell->getCoordinate()] = $cell;

        foreach ($cells as $cell) {
            foreach ($processor->reset($cell->getCoordinate())->getAdjacentCells($battlefield, 1, 0, CellModel::FLAG_SHIP) as $waterCell) {
                CellModel::switchPhase($waterCell, CellModel::FLAG_SKIP);
            }
        }
    }

    /**
     * @param Battlefield $battlefield
     * @param Cell        $cell - this cell will be attacked if it will be human player's turn
     *
     * @return Cell
     */
    private function processPlayerTurn(Battlefield $battlefield, Cell $cell) : Cell
    {
        return PlayerModel::isAIControlled($battlefield->getPlayer())
            ? CellModel::switchPhase($cell)
            : $this->ai->processCPUTurn($battlefield);
    }
}
