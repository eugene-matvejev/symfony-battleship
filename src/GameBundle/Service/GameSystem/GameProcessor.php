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
    public function processTurn(Cell $cell) : Game
    {
        $game = $cell->getBattlefield()->getGame();

        if (null !== $game->getResult()) {
            throw new GameProcessorException("game: {$game->getId()} already has result");
        }

        foreach ($game->getBattlefields() as $battlefield) {
            $attacker = $battlefield->getPlayer();
            if ($this->processPlayerTurnOnBattlefields($game, $attacker, $cell)) {
                $result = (new GameResult())
                    ->setPlayer($attacker);
                $game->setResult($result);

                break;
            }
        }

        return $game;
    }

    /**
     * @since 21.1
     *
     * @param Game   $game
     * @param Player $attacker
     * @param Cell   $cell
     *
     * @return bool - true if game been won, otherwise false
     */
    protected function processPlayerTurnOnBattlefields(Game $game, Player $attacker, Cell $cell) : bool
    {
        foreach ($game->getBattlefields() as $battlefield) {
            try {
                if ($this->processPlayerTurnOnBattlefield($battlefield, $attacker, $cell)) {
                    return true;
                }
            } catch (GameProcessorException $e) {
                continue;
            }
        }

        return false;
    }

    /**
     * @since 21.0
     *
     * @param Battlefield $battlefield
     * @param Player      $attacker
     * @param Cell        $cell
     *
     * @return bool - true if target battlefield do not have any life ships, otherwise false
     * @throws GameProcessorException
     */
    protected function processPlayerTurnOnBattlefield(Battlefield $battlefield, Player $attacker, Cell $cell) : bool
    {
        /** do not process turn on itself */
        if ($battlefield->getPlayer() === $attacker) {
            throw new GameProcessorException('player attacked itself');
        }

        $cell = $this->processPlayerTurn($battlefield, $cell);
        if (CellModel::isShipDead($cell)) {
            BattlefieldModel::flagWaterAroundShip($cell);

            return !BattlefieldModel::hasUnfinishedShips($battlefield);
        }

        return false;
    }

    /**
     * @param Battlefield $battlefield
     * @param Cell        $cell - this cell will be attacked if attacker is human
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
