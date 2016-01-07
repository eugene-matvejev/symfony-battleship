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
use GameBundle\Repository\BattlefieldRepository;
use GameBundle\Repository\CellStateRepository;
use GameBundle\Repository\GameResultRepository;
use GameBundle\Repository\PlayerTypeRepository;

class GameModel
{
    /**
     * @var BattlefieldRepository
     */
    private $battlefieldRepository;
    /**
     * @var EntityRepository
     */
    private $cellRepository;
    /**
     * @var CellStateRepository
     */
    private $cellStateRepository;
    /**
     * @var EntityRepository
     */
    private $gameRepository;
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;
    /**
     * @var EntityRepository
     */
    private $playerRepository;
    /**
     * @var PlayerTypeRepository
     */
    private $playerTypeRepository;
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var CellModel
     */
    private $cellModel;

    function __construct(ObjectManager $om, CellModel $cellModel, AI $ai)
    {
        $this->battlefieldRepository = $om->getRepository('GameBundle:Battlefield');
        $this->cellRepository        = $om->getRepository('GameBundle:Cell');
        $this->cellStateRepository   = $om->getRepository('GameBundle:CellState');
        $this->gameRepository        = $om->getRepository('GameBundle:Game');
        $this->gameResultRepository  = $om->getRepository('GameBundle:GameResult');
        $this->playerRepository      = $om->getRepository('GameBundle:Player');
        $this->playerTypeRepository  = $om->getRepository('GameBundle:PlayerType');
        $this->om                    = $om;
        $this->cellModel             = $cellModel;
        $this->ai                    = $ai;
    }

    /**
     * @param string $json
     *
     * @return \stdClass
     */
    public function init($json)
    {
        if(empty($json))
            return false;

        $json = json_decode($json);

        $game = new Game();
        $this->om->persist($game);

        $playerTypes = $this->playerTypeRepository->getTypes();
        foreach($json->data as $_player) {
            if(null === $player = $this->playerRepository->findOneBy(['name' => $_player->player->name])) {
                $player = (new Player())
                    ->setName($_player->player->name)
                    ->setType($playerTypes[PlayerModel::TYPE_HUMAN]);
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

        return $this->getJSON($game);
    }

    /**
     * @param Game $game
     *
     * @return \stdClass
     */
    public function getJSON(Game $game)
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
                $json->cells[] = CellModel::getJSON($cell, true);
            }

            $std->data[] = $json;
        }

        return $std;
    }

    /**
     * @param Battlefield $battlefield
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
     */
    public function nextTurn($json)
    {
        if(empty($json))
            return false;

        $json = json_decode($json);
        $std  = new \stdClass();
        $game = $this->gameRepository->find($json->game->id);
        if(null === $game) {
            throw new  GameException(__FUNCTION__ .' game: '. $json->game->id);
        }

        if(null !== $game->getResult()) {
            $std->victory = new \stdClass();

            return $std;
        }

        foreach($game->getBattlefields() as $battlefield) {
            /** @var Battlefield $battlefield */
//            $std->{$battlefield->getPlayer()->getId()} =
            $this->playerTurn($battlefield, $json);

            if($this->detectVictory($battlefield)) {
                $std->victory = new \stdClass();
                $std->victory->pid = $battlefield->getPlayer()->getId();

                return $std;
            }
        }

        foreach(CellModel::getChangedCells() as $cell) {
            $std->{$cell->getId()} = CellModel::getJSON($cell);
        }

        return $std;
    }

    /**
     * @param Battlefield $battlefield
     * @param \stdClass $json
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
//                        $_cell = $cell;
                        $_cell = $this->cellModel->switchState($cell);
                        break;
                    }
                }
                break;
        }

        $this->om->persist($_cell);
        $this->om->flush();

//        return CellModel::getJSON($_cell);
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return bool
     */
    public function detectVictory(Battlefield $battlefield)
    {
        $game = $battlefield->getGame();
        if(null !== $game->getResult()) {
            return true;
        }

        if(true !== BattlefieldModel::isUnfinished($battlefield)) {
            return false;
        }

        $result = (new GameResult())
            ->setPlayer($battlefield->getPlayer());
        $game->setResult($result);

        $this->om->persist($game);
        $this->om->flush();

        return true;
    }
}
