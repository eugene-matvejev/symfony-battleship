<?php

namespace EM\GameBundle\Service\GameSystem;

use EM\GameBundle\Entity\Game;
use EM\FoundationBundle\Entity\User;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\FoundationBundle\Model\UserModel;
use EM\GameBundle\Request\GameInitiationRequest;

/**
 * @see   GameBuilderTest
 *
 * @since 21.0
 */
class GameBuilder
{
    /**
     * @var UserModel
     */
    private $userModel;

    public function __construct(UserModel $UserModel)
    {
        $this->userModel = $UserModel;
    }

    protected function attachAIBattlefields(Game $game, int $amount, int $size)
    {
        for ($i = 0; $i < $amount; $i++) {
            $user = $this->userModel->createOnRequestAIControlled("CPU {$i}");

            /** hard-code ship into B2 for testing purposes */
            $battlefield = BattlefieldModel::generate($size, ['B2'])
                ->setUser($user);
            $game->addBattlefield($battlefield);
        }
    }

    public function buildGame(GameInitiationRequest $request, User $user) : Game
    {
        $game = new Game();
        $this->attachAIBattlefields($game, $request->getOpponents(), $request->getSize());

        $battlefield = BattlefieldModel::generate($request->getSize(), $request->getCoordinates());
        $battlefield->setUser($user);
        $game->addBattlefield($battlefield);

        /** for test purposes only - mark player cells as damaged */
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD_SHIP);

        return $game;
    }
}
