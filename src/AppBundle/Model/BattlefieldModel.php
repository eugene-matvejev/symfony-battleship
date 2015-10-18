<?php

namespace AppBundle\Model;

use AppBundle\Entity\Battlefield;
use AppBundle\Entity\CellState;
use AppBundle\Entity\Cell;
use AppBundle\Entity\Game;
use AppBundle\Entity\Player;
use AppBundle\Library\AI\AI;
use AppBundle\Repository\BattlefieldRepository;
use AppBundle\Repository\CellRepository;
use AppBundle\Repository\CellStateRepository;
use AppBundle\Repository\GameRepository;
use AppBundle\Repository\PlayerRepository;
use AppBundle\Repository\PlayerTypeRepository;
use Doctrine\Common\Persistence\ObjectManager;

class BattlefieldModel
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
     * @var CellState[]
     */
    private $cellStates;

    /**
     * @var CellModel
     */
    private $cellModel;

    /**
     * @param CellState[] $cellStates
     */
    public function __construct(array $cellStates)
    {
        $this->cellStates = $cellStates;
        $this->cellModel  = (new CellModel())->setCellStates($cellStates);
        $this->ai         = (new AI())->setCellModel($this->cellModel);
    }

    /**
     * @param BattlefieldRepository $repo
     *
     * @return $this
     */
    public function setBattlefieldRepository(BattlefieldRepository $repo)
    {
        $this->battlefieldRepository = $repo;

        return $this;
    }

    /**
     * @param CellRepository $repo
     *
     * @return $this
     */
    public function setCellRepository(CellRepository $repo)
    {
        $this->cellRepository = $repo;

        return $this;
    }

    /**
     * @param CellStateRepository $repo
     *
     * @return $this
     */
    public function setCellStateRepository(CellStateRepository $repo)
    {
        $this->cellStateRepository = $repo;

        return $this;
    }

    /**
     * @param GameRepository $repo
     *
     * @return $this
     */
    public function setGameRepository(GameRepository $repo)
    {
        $this->gameRepository = $repo;

        return $this;
    }

    /**
     * @param PlayerRepository $repo
     *
     * @return $this
     */
    public function setPlayerRepository(PlayerRepository $repo)
    {
        $this->playerRepository = $repo;

        return $this;
    }

    /**
     * @param PlayerTypeRepository $repo
     *
     * @return $this
     */
    public function setPlayerTypeRepository(PlayerTypeRepository $repo)
    {
        $this->playerTypeRepository = $repo;

        return $this;
    }

    /**
     * @param ObjectManager $entityManager
     *
     * @return $this
     */
    public function setEntityManager(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @param \stdClass $json
     *
     * @return \stdClass
     */
    public function save(\stdClass $json)
    {
        $game = $this->gameRepository->findOneBy(['id' => $json->id]);
        if(!$game instanceof Game) {
            $game = new Game();
            $this->entityManager->persist($game);
            $this->entityManager->flush();
            $json->id = $game->getId();
        }

        $playerStates = $this->playerTypeRepository->getTypes();
        foreach($json->data as $participant) {
            $player = $this->playerRepository->findOneBy(['name' => $participant->player->name]);

            if(!$player instanceof Player) {
                $player = (new Player())
                    ->setName($participant->player->name)
                    ->setType($playerStates[PlayerModel::TYPE_HUMAN]);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
            }
            $participant->player->id = $player->getId();

            $battlefield = $this->battlefieldRepository->findOneBy(['player' => $player, 'game' => $game]);
            if(!$battlefield instanceof Battlefield) {
                $battlefield = (new Battlefield())
                    ->setGame($game)
                    ->setPlayer($player);
                $this->entityManager->persist($battlefield);
                $this->entityManager->flush();
                $participant->battlefield->id = $battlefield->getId();
            }


            foreach($participant->cells as $cellData) {
                $cell = (new Cell())
                    ->setX($cellData->x)
                    ->setY($cellData->y)
                    ->setState($this->cellStates[$cellData->s])
                    ->setBattlefield($battlefield);

//                if($participant->player->type = PlayerModel::TYPE_HUMAN && !$this->ai->isTurnDoneForPlayer($battlefield->getPlayer())) {
//                    $this->ai->turn($cell);
//                }

                $this->entityManager->persist($cell);
            }
            $this->entityManager->flush();
            if($battlefield->getPlayer()->getType()->getId() == PlayerModel::TYPE_CPU) {
                $json->debug = $this->initCPUBattlefield($battlefield);
            }

        }

//        $_game = $this->gameRepository->find(['id' => $game->getId()]);
//        /**
//         * @var $_battlefields Battlefield[]
//         */
//        $_battlefields = $this->battlefieldRepository->findBy(['game' => $_game]);
//        foreach($_battlefields as $_battlefield) {
//        }
        return $json;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return \stdClass
     */
    public function initCPUBattlefield(Battlefield $battlefield)
    {
        foreach($this->cellRepository->findBy(['battlefield' => $battlefield]) as $cell) {
            /**
             * @var $cell Cell
             */
            if ($cell->getY() % 2 == 1) {
                $cell->setState($this->cellStates[CellModel::STATE_SHIP_LIVE]);

                $this->entityManager->persist($cell);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param \stdClass $json
     *
     * @return \stdClass
     */
    public function nextTurn(\stdClass $json)
    {
        $std = new \stdClass();

        $battlefields = $this->battlefieldRepository->findByGameId($json->game->id);

        foreach($battlefields as $battlefield) {
            $std->{$battlefield->getPlayer()->getId()} = $this->playerTurn($battlefield, $json);
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
                    if($cell->getX() != $json->x || $cell->getY() != $json->y)
                        continue;

                    $this->cellModel->switchState($cell);

                    $this->entityManager->persist($cell);
                    $this->entityManager->flush();

                    return CellModel::getJSON($cell);
                }
                break;
        }
    }
}