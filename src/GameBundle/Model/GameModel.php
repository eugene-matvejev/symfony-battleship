<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Response\GameTurnResponse;
use EM\GameBundle\Service\AI\AIService;

/**
 * @since 2.0
 */
class GameModel
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
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(AIService $ai, CellModel $cellModel, PlayerModel $playerModel, ObjectManager $om)
    {
        $this->ai = $ai;
        $this->cellModel = $cellModel;
        $this->playerModel = $playerModel;
        $this->om = $om;
    }

    public function init(string $json) : Game
    {
        $game = new Game();
        $this->om->persist($game);

        foreach (json_decode($json)->data as $data) {
            if (null === $player = $this->om->getRepository('GameBundle:Player')->findOneBy(['name' => $data->player->name])) {
                $player = (new Player())
                    ->setName($data->player->name)
                    ->setType($this->playerModel->getTypes()[PlayerModel::TYPE_HUMAN]);
            }

            $battlefield = (new Battlefield())
                ->setGame($game)
                ->setPlayer($player);
            $game->addBattlefield($battlefield);

            foreach ($data->cells as $cellData) {
                $cell = (new Cell())
                    ->setCoordinate($cellData->coordinate)
                    ->setState($battlefield->getPlayer()->getType()->getId() !== PlayerModel::TYPE_CPU
                        ? $this->cellModel->getAllStates()[$cellData->state]
                        : $this->cellModel->getAllStates()[CellModel::STATE_WATER_LIVE]);
                $battlefield->addCell($cell);
            }

            if ($battlefield->getPlayer()->getType()->getId() === PlayerModel::TYPE_CPU) {
                $this->initCPUBattlefield($battlefield);
            }
        }
        $this->om->flush();

        return $game;
    }

    public function initCPUBattlefield(Battlefield $battlefield)
    {
        $liveShipState = $this->cellModel->getAllStates()[CellModel::STATE_SHIP_LIVE];

        $battlefield->getCellByCoordinate('B2')->setState($liveShipState);
    }

    /**
     * @param int $cellId
     *
     * @return GameTurnResponse
     * @throws CellException
     */
    public function nextTurn(int $cellId) : GameTurnResponse
    {
        if (null === $cell = $this->om->getRepository('GameBundle:Cell')->find($cellId)) {
            throw new CellException("Cell: {$cellId} doesn't exist");
        }

        $game = $cell->getBattlefield()->getGame();
        $response = new GameTurnResponse();

        if (null !== $game->getResult()) {
            $response->setGameResult($game->getResult());

            return $response;
        }

        foreach ($game->getBattlefields() as $battlefield) {
            $this->playerTurn($battlefield, $cell->getCoordinate());

            if (null !== $game->getResult()) {
                $response->setGameResult($game->getResult());
                break;
            }
        }

        foreach (CellModel::getChangedCells() as $cell) {
            $this->om->persist($cell);
        }
        $this->om->flush();

        $response->setCells(CellModel::getChangedCells());

        return $response;
    }

    public function playerTurn(Battlefield $battlefield, string $playerCellCoordinate)
    {
        $cell = null;
        switch ($battlefield->getPlayer()->getType()->getId()) {
            case PlayerModel::TYPE_HUMAN:
                $cell = $this->ai->turn($battlefield);
                break;
            case PlayerModel::TYPE_CPU:
                if (null !== $cell = $battlefield->getCellByCoordinate($playerCellCoordinate)) {
                    $this->cellModel->switchState($cell);
                }
                break;
        }

        if (null !== $cell) {
            $this->ai->getStrategyService()->isShipDead($cell);
            $this->detectVictory($battlefield);
        }
    }

    public function detectVictory(Battlefield $battlefield) : bool
    {
        if (BattlefieldModel::hasUnfinishedShips($battlefield)) {
            return false;
        }

        $game = $battlefield->getGame();
        foreach ($game->getBattlefields() as $_battlefield) {
            if ($battlefield->getPlayer() === $_battlefield->getPlayer()) {
                continue;
            }

            $result = (new GameResult())
                ->setPlayer($_battlefield->getPlayer());

            $game->setResult($result);
        }

        return true;
    }
}
