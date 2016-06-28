<?php

namespace EM\GameBundle\Service\GameSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Exception\GameProcessorException;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\GameBundle\Service\AI\AIService;

/**
 * @see   GameProcessorTest
 *
 * @since 10.0
 */
class GameProcessor
{
    /**
     * @var AIService
     */
    private $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * @param Cell $cell
     *
     * @return Game
     * @throws GameProcessorException
     */
    public function processGameTurn(Cell $cell) : Game
    {
        $game = $cell->getBattlefield()->getGame();

        if (null !== $game->getResult()) {
            throw new GameProcessorException("game: {$game->getId()} already has result");
        }

        foreach ($game->getBattlefields() as $attackerBattlefield) {
            $attacker = $attackerBattlefield->getPlayer();

            foreach ($game->getBattlefields() as $battlefield) {
                try {
                    if ($this->processPlayerTurnOnBattlefield($battlefield, $attacker, $cell)) {
                        return $game;
                    }
                } catch (GameProcessorException $e) {
                    continue;
                }
            }
        }

        return $game;
    }

    /**
     * @param Battlefield $battlefield
     * @param Player      $player
     * @param Cell        $cell
     *
     * @return bool
     * @throws GameProcessorException
     */
    protected function processPlayerTurnOnBattlefield(Battlefield $battlefield, Player $player, Cell $cell) : bool
    {
        /** do not process turn on itself */
        if ($battlefield->getPlayer() === $player) {
            throw new GameProcessorException('player attacked itself');
        }

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
    protected function processPlayerTurn(Battlefield $battlefield, Cell $cell) : Cell
    {
        return PlayerModel::isAIControlled($battlefield->getPlayer())
            ? CellModel::switchPhase($cell)
            : $this->ai->processCPUTurn($battlefield);
    }
}
