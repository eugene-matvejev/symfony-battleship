<?php

namespace AppBundle\Model;

use AppBundle\Entity\Battlefield;
use AppBundle\Entity\CellState;
use AppBundle\Entity\Cell;
use AppBundle\Entity\Game;
use AppBundle\Entity\Player;
use AppBundle\Entity\PlayerType;
use AppBundle\Library\AI\Core;
use AppBundle\Repository\BattlefieldRepository;
use AppBundle\Repository\CellRepository;
use AppBundle\Repository\CellStateRepository;
use AppBundle\Repository\GameRepository;
use AppBundle\Repository\PlayerRepository;
use AppBundle\Repository\PlayerTypeRepository;
use Doctrine\Common\Persistence\ObjectManager;
use MyProject\Proxies\__CG__\stdClass;

class BattlefieldModel {
    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    /**
     * @var PlayerTypeRepository
     */
    private $playerTypeRepository;

    /**
     * @var GameRepository
     */
    private $gameRepository;

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
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var CellState[]
     */
    private $cellStates;

    /**
     * @param CellState[] $cellStates
     */
    public function __construct(array $cellStates) {
        $this->cellStates = $cellStates;
        $this->ai         = (new Core())->setCellStateModel((new CellStateModel())->setCellStates($cellStates));
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

            if($participant->player->type == PlayerModel::TYPE_CPU)
                $this->generateCPUBattlefield($participant);

            foreach($participant->cells as $cellData) {
                $cell = (new Cell())
                        ->setX($cellData->x)
                        ->setY($cellData->y)
                        ->setState($this->cellStates[$cellData->s])
                        ->setBattlefield($battlefield);

                if($participant->player->type = PlayerModel::TYPE_HUMAN && !$this->ai->isTurnDone()) {
                    $this->ai->turn($cell);
                }

                $this->entityManager->persist($cell);
            }
            $this->entityManager->flush();
        }

        return $json;
    }

    /**
     * @param \stdClass $json
     */
    public function generateCPUBattlefield(\stdClass $json) {

    }

    /**
     * @param \stdClass $json
     *
     * @return \stdClass
     */
    public function playerTurn(\stdClass $json) {
        $player      = $this->playerRepository->findOneBy(['id' => $json->game->player->id]);
        $game        = $this->gameRepository->findOneBy(['id' => $json->game->id]);
        $battlefield = $this->battlefieldRepository->findOneBy(['player' => $player, 'game' => $game]);
        $cell        = $this->cellRepository->findOneBy(['battlefield' => $battlefield, 'x' => $json->x, 'y' => $json->y]);

        /**
         * @var $battlefield Battlefield
         * @var $player Player
         * @var $game Game
         * @var $cell Cell
         */

        (new CellStateModel())
            ->setCellStates($this->cellStates)
            ->swapStatus($cell);

        $this->entityManager->persist($cell);
        $this->entityManager->flush();

        $turnJSON = new \stdClass();
        $turnJSON->x = $cell->getX();
        $turnJSON->y = $cell->getY();
        $turnJSON->s = $cell->getState()->getId();
        $turnJSON->pid = $player->getId();

        return $turnJSON;
    }

    /**
     * @param \stdClass $json
     *
     * @return \stdClass
     */
    public function AITurn(\stdClass $json)
    {
        $game         = $this->gameRepository->findById($json->game->id);
        $battlefields = $this->battlefieldRepository->findNotCPUsByGame($game);

        $turnJSON    = new \stdClass();
        $turnJSON->game = $game->getId();
//        $turnJSON->battlefield = $battlefields;
//        $turnJSON->battlefield = $battlefields[0]->getId();
//        return $turnJSON->text = print_r($battlefields, true);
        foreach($battlefields as $battlefield) {
            $turnJSON->{$battlefield->getId()} = $battlefield->getPlayer()->getId();
//            $std = new \stdClass();
//            foreach($battlefield->getCells() as $cell) {
//                if($this->ai->isTurnDone())
//                    return $turnJSON;
//
//                $this->ai->turn($cell);
//                $this->entityManager->persist($cell);
//                $this->entityManager->flush();
//
//                $std->x = $cell->getX();
//                $std->y = $cell->getY();
//                $std->s = $cell->getState()->getId();
//                $std->pid = $battlefield->getPlayer()->getId();
//            }
//            $turnJSON->{$battlefield->getPlayer()->getId()} = $std;
        }

        return $turnJSON;
    }
}