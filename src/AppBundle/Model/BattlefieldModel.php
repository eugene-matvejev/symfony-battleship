<?php

namespace AppBundle\Model;

use AppBundle\Entity\BattlefieldEntity;
use AppBundle\Entity\CellStateEntity;
use AppBundle\Entity\CellEntity;
use AppBundle\Entity\GameEntity;
use AppBundle\Entity\PlayerEntity;
use AppBundle\Library\AI\Core;
use AppBundle\Repository\BattlefieldRepository;
use AppBundle\Repository\CellRepository;
use AppBundle\Repository\CellStateRepository;
use AppBundle\Repository\GameRepository;
use AppBundle\Repository\PlayerRepository;
use Doctrine\Common\Persistence\ObjectManager;

class BattlefieldModel {
    /**
     * @var PlayerRepository
     */
    private $playerRepository;

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
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var \stdClass
     */
    private $json;

    /**
     * @var CellStateEntity[]
     */
    private $cellStates;

    /**
     * @param \stdClass         $json
     * @param CellStateEntity[] $cellStates
     */
    public function __construct(\stdClass $json, array $cellStates) {
        $this->json       = $json;
        $this->cellStates = $cellStates;
        $cellStateModel   = (new CellStateModel())
                ->setCellStates($cellStates);
        $this->ai         = (new Core())
                ->setCellStateModel($cellStateModel);
    }

    /**
     * @param PlayerRepository $playerRepository
     * @return $this
     */
    public function setPlayerRepository(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;

        return $this;
    }

    /**
     * @param GameRepository $gameRepository
     * @return $this
     */
    public function setGameRepository(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;

        return $this;
    }

    /**
     * @param BattlefieldRepository $battlefieldRepository
     * @return $this
     */
    public function setBattlefieldRepository(BattlefieldRepository $battlefieldRepository)
    {
        $this->battlefieldRepository = $battlefieldRepository;

        return $this;
    }

    /**
     * @param CellRepository $cellRepository
     * @return $this
     */
    public function setCellRepository(CellRepository $cellRepository)
    {
        $this->cellRepository = $cellRepository;

        return $this;
    }

    /**
     * @param CellStateRepository $cellStateRepository
     * @return $this
     */
    public function setCellStateRepository(CellStateRepository $cellStateRepository)
    {
        $this->cellStateRepository = $cellStateRepository;

        return $this;
    }

    /**
     * @param ObjectManager $entityManager
     * @return $this
     */
    public function setEntityManager(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     *
     */
    public function save()
    {

        $game = $this->gameRepository->findOneBy(['id' => $this->json->id]);
        if(!$game instanceof GameEntity) {
            $game = (new GameEntity())
                    ->setName($this->json->name);
            $this->entityManager->persist($game);
            $this->entityManager->flush();
            $this->json->id = $game->getId();
        }

        foreach($this->json->data as $json) {
            $player = $this->playerRepository->findOneBy(['name' => $json->player->name]);
            if(!$player instanceof PlayerEntity) {
                $player = (new PlayerEntity())
                    ->setName($json->player->name);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
            }
            $json->player->id = $player->getId();

            $battlefield = $this->battlefieldRepository->findOneBy(['player' => $player, 'game' => $game]);
            if(!$battlefield instanceof BattlefieldEntity) {
                $battlefield = (new BattlefieldEntity())
                    ->setGame($game)
                    ->setPlayer($player);
                $this->entityManager->persist($battlefield);
                $this->entityManager->flush();
                $json->battlefield->id = $battlefield->getId();
            }

            foreach($json->cells as $cellData) {
                $cell = (new CellEntity())
                        ->setX($cellData->x)
                        ->setY($cellData->y)
                        ->setState($this->cellStates[$cellData->s])
                        ->setBattlefield($battlefield);

                if($json->player->name == 'CPU' && !$this->ai->isTurnDone()) {
                    $this->ai->turn($cell);
                }

                $this->entityManager->persist($cell);
            }
            $this->entityManager->flush();
        }
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
         * @var $battlefield BattlefieldEntity
         * @var $player PlayerEntity
         * @var $game GameEntity
         * @var $cell CellEntity
         */

        $cellStateModel = (new CellStateModel())
                        ->setCellStates($this->cellStates);
        $cellStateModel->swapStatus($cell);

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
        $turnJSON    = new \stdClass();
        $cpuPlayer   = $this->playerRepository->findOneBy(['name' => 'CPU']);
        $game        = $this->gameRepository->findOneBy(['id' => $json->game->id]);
        $battlefield = $this->battlefieldRepository->findOneBy(['player' => $cpuPlayer, 'game' => $game]);
        /**
         * @var $battlefield BattlefieldEntity
         * @var $cpuPlayer PlayerEntity
         * @var $game GameEntity
         */

        foreach($battlefield->getCells() as $cell) {
            if($this->ai->isTurnDone())
                return $turnJSON;

            $this->ai->turn($cell);
            $this->entityManager->persist($cell);
            $this->entityManager->flush();

            $turnJSON->x = $cell->getX();
            $turnJSON->y = $cell->getY();
            $turnJSON->s = $cell->getState()->getId();
            $turnJSON->pid = $cpuPlayer->getId();
        }

        return $turnJSON;
    }
}