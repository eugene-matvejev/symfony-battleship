<?php
namespace AppBundle\Model;

use AppBundle\Entity\Battlefield;
use AppBundle\Entity\Cell;
use AppBundle\Entity\Game;
use AppBundle\Entity\GameResult;
use AppBundle\Entity\Player;
use AppBundle\Library\AI\AI;
use AppBundle\Repository\BattlefieldRepository;
use AppBundle\Repository\CellStateRepository;
use AppBundle\Repository\GameResultRepository;
use AppBundle\Repository\PlayerTypeRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

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
        $this->battlefieldRepository = $om->getRepository('AppBundle:Battlefield');
        $this->cellRepository        = $om->getRepository('AppBundle:Cell');
        $this->cellStateRepository   = $om->getRepository('AppBundle:CellState');
        $this->gameRepository        = $om->getRepository('AppBundle:Game');
        $this->gameResultRepository  = $om->getRepository('AppBundle:GameResult');
        $this->playerRepository      = $om->getRepository('AppBundle:Player');
        $this->playerTypeRepository  = $om->getRepository('AppBundle:PlayerType');
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
            $player = $this->playerRepository->findOneBy(['name' => $_player->player->name]);

            if(!$player instanceof Player) {
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
                    ->setState($battlefield->getPlayer()->getType()->getId() != PlayerModel::TYPE_CPU
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
            $_json = new \stdClass();
            $_json->id = $battlefield->getId();
            $_json->player = PlayerModel::getJSON($battlefield->getPlayer());
            $_json->cells = [];

            foreach($battlefield->getCells() as $cell) {
                $_json->cells[] = CellModel::getJSON($cell, true);
            }

            $std->data[] = $_json;
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
        if(null !== $game->getResult()) {
            $std->victory = new \stdClass();
//            $std->victory->pid = $game->getResult()->getWinner()->getId();

            return $std;
        }

        foreach($game->getBattlefields() as $battlefield) {
            /** @var Battlefield $battlefield */
            $std->{$battlefield->getPlayer()->getId()} = $this->playerTurn($battlefield, $json);

            if($this->detectVictory($battlefield)) {
                $std->victory = new \stdClass();
                $std->victory->pid = $battlefield->getPlayer()->getId();

                return $std;
            }
        }

        return $std;
    }

    /**
     * @param Battlefield $battlefield
     * @param \stdClass $json
     *
     * @return \stdClass
     */
    public function playerTurn(Battlefield $battlefield, \stdClass $json)
    {
        $_cell = null;
        switch($battlefield->getPlayer()->getType()->getId()) {
            case PlayerModel::TYPE_HUMAN:
                $_cell = $this->ai->turn($battlefield);
                break;
            default:
            case PlayerModel::TYPE_CPU:
                foreach($battlefield->getCells() as $cell) {
                    if($cell->getX() !== $json->cell->x || $cell->getY() !== $json->cell->y)
                        continue;
                    $_cell = $cell;
                    $this->cellModel->switchState($cell);
                    break;
                }
                break;
        }


        $this->om->persist($_cell);
        $this->om->flush();

        return CellModel::getJSON($_cell);
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return bool
     */
    public function detectVictory(Battlefield $battlefield) : \bool
    {
        foreach($battlefield->getCells() as $cell) {
            if($cell->getState()->getId() === CellModel::STATE_SHIP_LIVE)
                return false;
        }

        $game = $battlefield->getGame();
        if($game->getResult() !== null) {
            return true;
        }

        $result = (new GameResult())
            ->setWinner($battlefield->getPlayer());
        $game->setResult($result);

        $this->om->persist($game);
        $this->om->flush();

        return true;
    }
}
