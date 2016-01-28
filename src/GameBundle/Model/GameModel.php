<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\AI\AI;
use EM\GameBundle\AI\AIStrategy;
use EM\GameBundle\Exception\GameException;
use Symfony\Bridge\Monolog\Logger;

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
    /**
     * @var Logger
     */
    private $logger;

    function __construct(ObjectManager $om, CellModel $cellModel, PlayerModel $playerModel, AI $ai, AIStrategy $strategy, Logger $logger)
    {
        $this->om               = $om;
        $this->gameRepository   = $om->getRepository('GameBundle:Game');
        $this->playerRepository = $om->getRepository('GameBundle:Player');
        $this->ai               = $ai;
        $this->strategyService  = $strategy;
        $this->cellModel        = $cellModel;
        $this->playerModel      = $playerModel;
        $this->logger           = $logger;
    }

    /**
     * verify, init and save game
     *
     * @param string $json
     * @return \stdClass
     */
    public function init(string $json) : \stdClass
    {
        $json = json_decode($json);

        $game = new Game();
        $this->om->persist($game);

        foreach($json->data as $_player) {
            if(null === $player = $this->playerRepository->findOneBy(['name' => $_player->player->name])) {
                $player = (new Player())
                    ->setName($_player->player->name)
                    ->setType($this->playerModel->getTypes()[PlayerModel::TYPE_HUMAN]);
            }

            $battlefield = (new Battlefield())
                ->setGame($game)
                ->setPlayer($player);
            $game->addBattlefield($battlefield);

            foreach($_player->cells as $_cell) {
                $cell = (new Cell())
                    ->setX($_cell->x)
                    ->setY($_cell->y)
                    ->setState($battlefield->getPlayer()->getType()->getId() !== PlayerModel::TYPE_CPU
                        ? $this->cellModel->getCellStates()[$_cell->s]
                        : $this->cellModel->getCellStates()[CellModel::STATE_WATER_LIVE]);
                $battlefield->addCell($cell);
            }

            if($battlefield->getPlayer()->getType()->getId() === PlayerModel::TYPE_CPU) {
                $this->initCPUShips($battlefield);
            }
        }
        $this->om->flush();

        return self::getJSON($game);
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return void
     */
    public function initCPUShips(Battlefield $battlefield)
    {
        foreach($battlefield->getCells() as $cell) {
            if(($cell->getX() === 1 && $cell->getY() === 1) || ($cell->getX() === 1 && $cell->getY() === 5)) {
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

        if(null === $game = $this->gameRepository->find($arr->game->id)) {
            throw new GameException(__FUNCTION__ .' game: '. $arr->game->id .' doesn\'t exists.');
        }

        if(null !== $game->getResult()) {
            return $std->victory = GameResultModel::getJSON($game->getResult());
        }

        foreach($game->getBattlefields() as $battlefield) {
            $this->playerTurn($battlefield, $arr);

            if(null !== $game->getResult() || $this->detectVictory($battlefield)) {
                $std->victory = GameResultModel::getJSON($game->getResult());

                break;
            }
        }

        foreach(CellModel::getChangedCells() as $cell) {
            if(!isset($std->{$cell->getBattlefield()->getId()})) {
                $std->{$cell->getBattlefield()->getId()} = [];
            }

            $std->{$cell->getBattlefield()->getId()}[] = CellModel::getJSON($cell);
            $this->logger->addEmergency(__CLASS__ .':'. __FUNCTION__ . ' :: cell: '. print_r(CellModel::getJSON($cell), true));
        }
        $this->om->flush();

        return $std;
    }

    /**
     * @param Battlefield $battlefield
     * @param \stdClass $json
     *
     * @return void
     */
    public function playerTurn(Battlefield $battlefield, \stdClass $json)
    {
        $_cell = null;
        switch($battlefield->getPlayer()->getType()->getId()) {
            case PlayerModel::TYPE_HUMAN:
                $_cell = $this->ai->turn($battlefield);
                break;
            case PlayerModel::TYPE_CPU:
                foreach($battlefield->getCells() as $cell) {
                    if($cell->getX() === $json->cell->x && $cell->getY() === $json->cell->y) {
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
        if(true !== BattlefieldModel::isUnfinished($battlefield)) {
            return false;
        }

        $game = $battlefield->getGame();
        foreach($game->getBattlefields() as $_battlefield) {
            if($_battlefield->getId() !== $battlefield->getId()) {
                $result = (new GameResult())
                    ->setPlayer($_battlefield->getPlayer());
                $game->setResult($result);

                $this->om->persist($game);
                return true;
            }
        }

        return false;
    }

    public static function getJSON(Game $game) : \stdClass
    {
        $std = new \stdClass();
        $std->id = $game->getId();
        $std->data = [];

        foreach($game->getBattlefields() as $battlefield) {
            $std->data[] = BattlefieldModel::getJSON($battlefield);
        }

        return $std;
    }
}
