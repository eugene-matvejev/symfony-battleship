<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use EM\GameBundle\AI\AI;
use EM\GameBundle\AI\AIStrategy;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Exception\GameException;

/**
 * @since 2.0
 */
class GameModel
{
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
    /**
     * @var AI
     */
    private $ai;
    /**
     * @var AIStrategy
     */
    private $strategyService;
    /**
     * @var CellModel
     */
    private $cellModel;
    /**
     * @var PlayerModel
     */
    private $playerModel;

    function __construct(ObjectManager $om, CellModel $cellModel, PlayerModel $playerModel, AI $aiService, AIStrategy $aiStrategy)
    {
        $this->om = $om;
        $this->gameRepository = $om->getRepository('GameBundle:Game');
        $this->playerRepository = $om->getRepository('GameBundle:Player');
        $this->cellModel = $cellModel;
        $this->playerModel = $playerModel;
        $this->ai = $aiService;
        $this->strategyService = $aiStrategy;
    }

    /**
     * verify, init and save game
     *
     * @param string $json
     *
     * @return \stdClass
     */
    public function init(string $json) : \stdClass
    {
        $game = new Game();
        $this->om->persist($game);

        foreach (json_decode($json)->data as $_player) {
            if (null === $player = $this->playerRepository->findOneBy(['name' => $_player->player->name])) {
                $player = (new Player())
                    ->setName($_player->player->name)
                    ->setType($this->playerModel->getTypes()[PlayerModel::TYPE_HUMAN]);
            }

            $battlefield = (new Battlefield())
                ->setGame($game)
                ->setPlayer($player);
            $game->addBattlefield($battlefield);

            foreach ($_player->cells as $_cell) {
                $cell = (new Cell())
                    ->setX($_cell->x)
                    ->setY($_cell->y)
                    ->setState($battlefield->getPlayer()->getType()->getId() !== PlayerModel::TYPE_CPU
                        ? $this->cellModel->getCellStates()[$_cell->s]
                        : $this->cellModel->getCellStates()[CellModel::STATE_WATER_LIVE]);
                $battlefield->addCell($cell);
            }

            if ($battlefield->getPlayer()->getType()->getId() === PlayerModel::TYPE_CPU) {
                $this->initCPUShips($battlefield);
            }
        }
        $this->om->flush();

        return self::getJSON($game);
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
     * @return \stdClass
     * @throws GameException
     */
    public function nextTurn(string $json) : \stdClass
    {
        $arr = json_decode($json);
        $std = new \stdClass();

        if (null === $game = $this->gameRepository->find($arr->game->id)) {
            throw new GameException(__FUNCTION__ . ' game: ' . $arr->game->id . ' doesn\'t exists.');
        }

        if (null !== $game->getResult()) {
            return $std->victory = GameResultModel::getJSON($game->getResult());
        }

        foreach ($game->getBattlefields() as $battlefield) {
            $this->playerTurn($battlefield, $arr->cell);

            if (null !== $game->getResult() || $this->detectVictory($battlefield)) {
                $std->victory = GameResultModel::getJSON($game->getResult());
                break;
            }
        }

        $this->om->flush();

        foreach (CellModel::getChangedCells() as $cell) {
            if (!isset($std->{$cell->getBattlefield()->getId()})) {
                $std->{$cell->getBattlefield()->getId()} = [];
            }

            $std->{$cell->getBattlefield()->getId()}[] = CellModel::getJSON($cell);
        }

        return $std;
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

        $this->strategyService->isShipDead($_cell);
        $this->om->persist($_cell);
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
        $std = new \stdClass();
        $std->id = $game->getId();
        $std->data = [];

        foreach ($game->getBattlefields() as $battlefield) {
            $std->data[] = BattlefieldModel::getJSON($battlefield);
        }

        return $std;
    }
}
