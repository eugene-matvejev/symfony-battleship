<?php

namespace AppBundle\Model;

use AppBundle\Entity\Battlefield;
use AppBundle\Entity\CellState;
use AppBundle\Entity\Cell;
use AppBundle\Entity\Game;
use AppBundle\Entity\GameResult;
use AppBundle\Entity\Player;
use AppBundle\Library\AI\AI;
use AppBundle\Repository\BattlefieldRepository;
use AppBundle\Repository\CellRepository;
use AppBundle\Repository\CellStateRepository;
use AppBundle\Repository\GameRepository;
use AppBundle\Repository\GameResultRepository;
use AppBundle\Repository\PlayerRepository;
use AppBundle\Repository\PlayerTypeRepository;
use Doctrine\Common\Persistence\ObjectManager;

class GameModel
{
    /**
     * @var BattlefieldRepository
     */
    private $battlefieldRepository;

    /**
     * @var CellRepository
     */
    private $cellRepository;

    /**
     * @var CellStateRepository
     */
    private $cellStateRepository;

    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;

    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    /**
     * @var PlayerTypeRepository
     */
    private $playerTypeRepository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var CellModel
     */
    private $cellModel;

    function __construct(BattlefieldRepository $battlefieldRepository,
                         CellRepository $cellRepository,
                         CellStateRepository $cellStateRepository,
                         GameRepository $gameRepository,
                         GameResultRepository $gameResultRepository,
                         PlayerRepository $playerRepository,
                         PlayerTypeRepository $playerTypeRepository,
                         ObjectManager $entityManager,
                         CellModel $cellModel,
                         AI $ai
    ) {
        $this->battlefieldRepository = $battlefieldRepository;
        $this->cellRepository        = $cellRepository;
        $this->cellStateRepository   = $cellStateRepository;
        $this->gameRepository        = $gameRepository;
        $this->gameResultRepository  = $gameResultRepository;
        $this->playerRepository      = $playerRepository;
        $this->playerTypeRepository  = $playerTypeRepository;
        $this->entityManager         = $entityManager;
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
            return [];

        $json = json_decode($json);

        $game = $this->gameRepository->findOneBy(['id' => $json->id]);
        if(!$game instanceof Game) {
            $game = new Game();
            $this->entityManager->persist($game);
            $this->entityManager->flush();
            $json->id = $game->getId();
        }

        $playerStates = $this->playerTypeRepository->getTypes();
        foreach($json->data as $_player) {
            $player = $this->playerRepository->findOneBy(['name' => $_player->player->name]);

            if(!$player instanceof Player) {
                $player = (new Player())
                    ->setName($_player->player->name)
                    ->setType($playerStates[PlayerModel::TYPE_HUMAN]);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
            }
            $_player->player->id = $player->getId();

            $battlefield = $this->battlefieldRepository->findOneBy(['player' => $player, 'game' => $game]);
            if(!$battlefield instanceof Battlefield) {
                $battlefield = (new Battlefield())
                    ->setGame($game)
                    ->setPlayer($player);
                $this->entityManager->persist($battlefield);
                $this->entityManager->flush();
                $_player->battlefield->id = $battlefield->getId();
            }


            foreach($_player->cells as $_cell) {
                $cell = (new Cell())
                    ->setX($_cell->x)
                    ->setY($_cell->y)
                    ->setState($battlefield->getPlayer()->getType()->getId() != PlayerModel::TYPE_CPU
                        ? $this->cellModel->getCellStates()[$_cell->s]
                        : $this->cellModel->getCellStates()[CellModel::STATE_WATER_LIVE])
                    ->setBattlefield($battlefield);

                $this->entityManager->persist($cell);
            }

            $this->entityManager->flush();

            if($battlefield->getPlayer()->getType()->getId() == PlayerModel::TYPE_CPU) {
                $this->initCPUBattlefield($battlefield);
            }

        }

        return $json;
    }

    /**
     * @param Battlefield $battlefield
     */
    public function initCPUBattlefield(Battlefield $battlefield)
    {
        foreach($this->cellRepository->findBy(['battlefield' => $battlefield]) as $cell) {
            /**
             * @var $cell Cell
             */
            if($cell->getX() == 1 && $cell->getY() == 1)
                $cell->setState($this->cellModel->getCellStates()[CellModel::STATE_SHIP_LIVE]);
        }
        $this->entityManager->flush();
    }

    /**
     * @param string $json
     *
     * @return \stdClass
     */
    public function nextTurn($json)
    {
        if(empty($json))
            return [];

        //{"game":{"id":188},"player":{"id":1,"name":"CPU","type":1},"cell":{"x":0,"y":1}}

        $json = json_decode($json);
        $std  = new \stdClass();

        $battlefields = $this->battlefieldRepository->findByGameId($json->game->id);

        foreach($battlefields as $battlefield) {
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
        switch($battlefield->getPlayer()->getType()->getId()) {
            case PlayerModel::TYPE_HUMAN:
//            case PlayerModel::TYPE_CPU:
                foreach($battlefield->getCells() as $cell) {
                    if($this->ai->isTurnDoneForPlayer($cell->getBattlefield()->getPlayer()))
                        break;

                    if($this->ai->turn($cell)) {
                        $this->entityManager->persist($cell);
                        $this->entityManager->flush();

                        return CellModel::getJSON($cell);
                    }
                }
                break;
            case PlayerModel::TYPE_CPU:
//            case PlayerModel::TYPE_HUMAN:
                foreach($battlefield->getCells() as $cell) {
                    if($cell->getX() != $json->cell->x || $cell->getY() != $json->cell->y)
                        continue;

                    $this->cellModel->switchState($cell);

                    $this->entityManager->persist($cell);
                    $this->entityManager->flush();

                    return CellModel::getJSON($cell);
                }
                break;
        }
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return bool
     */
    public function detectVictory(Battlefield $battlefield)
    {
        foreach($battlefield->getCells() as $cell) {
            if($cell->getState()->getId() == CellModel::STATE_SHIP_LIVE)
                return false;
        }

        if($this->gameResultRepository->findOneBy(['game' => $battlefield->getGame()]) instanceof GameResult)
            return true;

        $gameResult = (new GameResult())
            ->setGame($battlefield->getGame())
            ->setWinner($battlefield->getPlayer());

        $this->entityManager->persist($gameResult);
        $this->entityManager->flush();
        return true;
    }
}
