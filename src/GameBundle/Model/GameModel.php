<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Exception\GameException;
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
    /**
     * @var EntityRepository
     */
    private $gameRepository;
    /**
     * @var EntityRepository
     */
    private $playerRepository;

    function __construct(AIService $ai, CellModel $cellModel, PlayerModel $playerModel, ObjectManager $om)
    {
        $this->ai = $ai;
        $this->cellModel = $cellModel;
        $this->playerModel = $playerModel;
        $this->om = $om;
        $this->gameRepository = $om->getRepository('GameBundle:Game');
        $this->playerRepository = $om->getRepository('GameBundle:Player');
    }

    public function init(string $json) : Game
    {
        $game = new Game();
        $this->om->persist($game);

        foreach (json_decode($json)->data as $data) {
            if (null === $player = $this->playerRepository->findOneBy(['name' => $data->player->name])) {
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
                    ->setX($cellData->x)
                    ->setY($cellData->y)
                    ->setState($battlefield->getPlayer()->getType()->getId() !== PlayerModel::TYPE_CPU
                        ? $this->cellModel->getCellStates()[$cellData->s]
                        : $this->cellModel->getCellStates()[CellModel::STATE_WATER_LIVE]);
                $battlefield->addCell($cell);
            }

            if ($battlefield->getPlayer()->getType()->getId() === PlayerModel::TYPE_CPU) {
                $this->initCPUShips($battlefield);
            }
        }
        $this->om->flush();

        return $game; //self::getJSON($game);
    }

    public function initCPUShips(Battlefield $battlefield)
    {
        foreach ($battlefield->getCells() as $cell) {
            if ($cell->getX() === 1 && $cell->getY() === 1) {
                $cell->setState($this->cellModel->getCellStates()[CellModel::STATE_SHIP_LIVE]);
            }
        }
    }

    /**
     * @param string $json
     *
     * @return GameTurnResponse
     * @throws GameException
     */
    public function nextTurn(string $json) : GameTurnResponse
    {
        $data = json_decode($json);

        if (null === $game = $this->gameRepository->find($data->game->id)) {
            throw new GameException(__FUNCTION__ . ' game: ' . $data->game->id . ' don\'t exists.');
        }

        $response = new GameTurnResponse();

        if (null !== $game->getResult()) {
            $response->setGameResult($game->getResult());

            return $response;
        }

        foreach ($game->getBattlefields() as $battlefield) {
            $this->playerTurn($battlefield, $data->cell);

            if (null !== $game->getResult()) {
                $response->setGameResult($game->getResult());
                break;
            }
        }

        $this->om->flush();

        foreach (CellModel::getChangedCells() as $cell) {
            $response->addCell($cell);
        }

        return $response;
    }

    public function playerTurn(Battlefield $battlefield, \stdClass $cellData)
    {
        $_cell = null;
        switch ($battlefield->getPlayer()->getType()->getId()) {
            case PlayerModel::TYPE_HUMAN:
                $_cell = $this->ai->turn($battlefield);
                break;
            case PlayerModel::TYPE_CPU:
                foreach ($battlefield->getCells() as $cell) {
                    if ($cell->getX() === $cellData->x && $cell->getY() === $cellData->y) {
                        $_cell = $this->cellModel->switchState($cell);
                        break;
                    }
                }
                break;
        }

        $this->ai->getStrategyService()->isShipDead($_cell);
        $this->om->persist($_cell);
        $this->detectVictory($battlefield);
    }

    public function detectVictory(Battlefield $battlefield) : bool
    {
        $game = $battlefield->getGame();
        if (BattlefieldModel::isUnfinished($battlefield)) {
            foreach ($game->getBattlefields() as $_battlefield) {
                if ($battlefield->getId() !== $_battlefield->getId()) {
                    $result = (new GameResult())
                        ->setPlayer($_battlefield->getPlayer());
                    $game->setResult($result);
                    $this->om->persist($result);

                    return true;
                }
            }
        }

        return false;
    }

    public static function getJSON(Game $game) : \stdClass
    {
        $data = [];
        foreach ($game->getBattlefields() as $battlefield) {
            $data[] = BattlefieldModel::getJSON($battlefield);
        }

        return (object)[
            'id' => $game->getId(),
            'data' => $data
        ];
    }
}
