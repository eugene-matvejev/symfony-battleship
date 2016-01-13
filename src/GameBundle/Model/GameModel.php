<?php

namespace GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Entity\Game;
use GameBundle\Entity\GameResult;
use GameBundle\Entity\Player;
use GameBundle\Library\AI\AI;
use GameBundle\Library\Exception\GameException;
use Symfony\Bridge\Monolog\Logger;

/**
 * @since 2.0
 */
class GameModel
{
    /**
     * @var EntityRepository
     */
    private $gameRepository;
    /**
     * @var EntityRepository
     */
    private $playerRepository;
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var CellModel
     */
    private $cellModel;

    function __construct(ObjectManager $om, Logger $logger, CellModel $cellModel, PlayerModel $playerModel, AI $ai)
    {
        $this->om               = $om;
        $this->logger           = $logger;
        $this->gameRepository   = $om->getRepository('GameBundle:Game');
        $this->playerRepository = $om->getRepository('GameBundle:Player');
        $this->ai               = $ai;
        $this->cellModel        = $cellModel;
        $this->playerModel      = $playerModel;
    }

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
                $this->om->persist($player);
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

            if($battlefield->getPlayer()->getType()->getId() == PlayerModel::TYPE_CPU) {
                $this->initCPUBattlefield($battlefield);
            }

        }
        $this->om->flush();

        return self::getJSON($game);
    }

    public static function getJSON(Game $game) : \stdClass
    {
        $std = new \stdClass();

        $std->id = $game->getId();
        $std->data = [];

        foreach($game->getBattlefields() as $battlefield) {
            $json = new \stdClass();
            $json->id = $battlefield->getId();
            $json->player = PlayerModel::getJSON($battlefield->getPlayer());
            $json->cells = [];

            foreach($battlefield->getCells() as $cell) {
                $json->cells[] = CellModel::getJSON($cell);
            }

            $std->data[] = $json;
        }

        return $std;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return void
     */
    public function initCPUBattlefield(Battlefield $battlefield)
    {
        foreach($battlefield->getCells() as $cell) {
            if($cell->getX() === 1 && $cell->getY() === 1) {
                $cell->setState($this->cellModel->getCellStates()[CellModel::STATE_SHIP_LIVE]);
                break;
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
        $json = json_decode($json);
        $std  = new \stdClass();

        if(null === $game = $this->gameRepository->find($json->game->id)) {
            throw new GameException(__FUNCTION__ .' game: '. $json->game->id .' doesn\'t exists.');
        }

        if(null !== $game->getResult()) {
            return $std->victory = GameResultModel::getJSON($game->getResult());
        }

        foreach($game->getBattlefields() as $battlefield) {
            /** @var Battlefield $battlefield */
            $this->playerTurn($battlefield, $json);

            if($this->detectVictory($battlefield)) {
                $std->victory = GameResultModel::getJSON($battlefield->getGame()->getResult());

                break;
            }
        }

        $log = [];
        foreach(CellModel::getChangedCells() as $cell) {
            $log[] = CellModel::getJSON($cell);
            $std->{$cell->getBattlefield()->getId()} = CellModel::getJSON($cell);
        }
        $this->logger->addDebug(__CLASS__ .':'. __FUNCTION__ . ' :: cells: '. print_r($log, true));


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

        $this->om->persist($_cell);
        $this->om->flush();
    }

    public function detectVictory(Battlefield $battlefield) : bool
    {
        $game = $battlefield->getGame();
        if(null !== $game->getResult()) {
            return true;
        }

        if(true !== BattlefieldModel::isUnfinished($battlefield)) {
            return false;
        }

        $winner = null;
        foreach($game->getBattlefields() as $_battlefield) {
            if($_battlefield->getId() !== $battlefield->getId()) {
                $winner = $_battlefield->getPlayer();
                break;
            }
        }

        $result = (new GameResult())
            ->setPlayer($winner);
        $game->setResult($result);

        $this->om->persist($game);
        $this->om->flush();

        return true;
    }
}
