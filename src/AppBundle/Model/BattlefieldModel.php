<?php

namespace AppBundle\Model;

use AppBundle\Entity\BattlefieldEntity;
use AppBundle\Entity\CellEntity;
use AppBundle\Entity\GameEntity;
use AppBundle\Entity\PlayerEntity;
use AppBundle\Repository\BattlefieldRepository;
use AppBundle\Repository\CellRepository;
use AppBundle\Repository\CellStateRepository;
use AppBundle\Repository\GameRepository;
use AppBundle\Repository\PlayerRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

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
     * @var CellStateRepository
     */
    private $cellStateRepository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     *
     */
    private $data;

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->data = $request->getContent();
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
     *
     */
    public function save() {
        $this->data = json_decode($this->data);

        $cellStates = [
            CellStateModel::WATER_LIVE => $this->cellStateRepository->findOneBy(['id' => CellStateModel::WATER_LIVE]),
            CellStateModel::WATER_DIED => $this->cellStateRepository->findOneBy(['id' => CellStateModel::WATER_DIED]),
            CellStateModel::SHIP_LIVE  => $this->cellStateRepository->findOneBy(['id' => CellStateModel::SHIP_LIVE]),
            CellStateModel::SHIP_LIVE  => $this->cellStateRepository->findOneBy(['id' => CellStateModel::SHIP_LIVE])
        ];

        foreach($this->data as $playerData) {
            $player = (new PlayerEntity())
                    ->setId($playerData->id)
                    ->setName($playerData->name);
            $this->entityManager->persist($player);

            $game = (new GameEntity());
            $this->entityManager->persist($game);

            $battlefield = (new BattlefieldEntity())
                        ->setGame($game)
                        ->setPlayer($player);
            $this->entityManager->persist($battlefield);

            foreach($playerData->data as $cellJSON) {
                $cell = (new CellEntity())
                        ->setX($cellJSON->x)
                        ->setY($cellJSON->y)
                        ->setState($cellStates[$cellJSON->s])
                        ->setBattlefield($battlefield);

                $this->entityManager->persist($cell);
            }
            $this->entityManager->flush();
        }
    }
}