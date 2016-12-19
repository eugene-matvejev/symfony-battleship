<?php

namespace EM\Tests\Environment\Factory;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\FoundationBundle\Entity\User;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\UserModel;

/**
 * @since 17.3
 */
class MockFactory
{
    public static function getBattlefieldMock(int $size = 7) : Battlefield
    {
        $battlefield = BattlefieldModel::generate($size)
            ->setPlayer(static::getPlayerMock(''));

        return $battlefield;
    }

    public static function getCellMock(string $coordinate, int $mask = CellModel::FLAG_NONE) : Cell
    {
        return (new Cell())
            ->setCoordinate($coordinate)
            ->setFlags($mask);
    }

    public static function getGameMock(int $players = 2, int $size = 7) : Game
    {
        $game = new Game();
        for ($i = 0; $i < $players; $i++) {
            $game->addBattlefield(static::getBattlefieldMock($size));
        }

        return $game;
    }

    public static function getGameResultMock(int $players = 2, int $battlefieldSize = 7) : GameResult
    {
        $game       = static::getGameMock($players, $battlefieldSize);
        $gameResult = (new GameResult());
        $game->setResult($gameResult);

        return $gameResult;
    }

    public static function getPlayerMock(string $email, int $flags = UserModel::FLAG_NONE) : Player
    {
        return (new Player())
            ->setEmail($email)
            ->setPasswordHash('mockedPassword')
            ->setFlags($flags);
    }

    public static function getAIPlayerMock(string $email) : Player
    {
        return static::getPlayerMock($email, UserModel::FLAG_AI_CONTROLLED);
    }
}
