<?php

namespace EM\GameBundle\Service\GameSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Exception\GameException;
use EM\GameBundle\Exception\PlayerException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Request\GameInitiationRequest;
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

        /** for test purposes only - mark player cells as damaged */
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD_SHIP);

        return $game;
    }

    /**
     * @param Cell $cell
     *
     * @return Game
     * @throws CellException
     * @throws GameException
     * @throws PlayerException
     */
    public function processGameTurn(Cell $cell) : Game
    {
        $game = $cell->getBattlefield()->getGame();

        if (null !== $game->getResult()) {
            throw new GameException("game: {$game->getId()} already has result");
        }

        foreach ($game->getBattlefields() as $turnOwnerBattlefield) {
            foreach ($game->getBattlefields() as $targetBattlefield) {
                /** do not process turn on itself */
                if ($turnOwnerBattlefield === $targetBattlefield) {
                    continue;
                }

                if ($this->processPlayerTurnOnBattlefield($targetBattlefield, $turnOwnerBattlefield->getPlayer(), $cell)) {
                    return $game;
                }
            }
        }

        return $game;
    }

    protected function processPlayerTurnOnBattlefield(Battlefield $battlefield, Player $player, Cell $cell) : bool
    {
        $cell = $this->processPlayerTurn($battlefield, $cell);

        if (CellModel::isShipDead($cell)) {
            BattlefieldModel::flagWaterAroundShip($cell);

            if (BattlefieldModel::hasUnfinishedShips($battlefield)) {
                return false;
            }

            $result = (new GameResult())
                ->setPlayer($player);
            $battlefield->getGame()->setResult($result);

            return true;
        }

        return false;
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
