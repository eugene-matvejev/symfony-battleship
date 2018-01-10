<?php

namespace EM\Tests\Environment\Factory;

use EM\FoundationBundle\Entity\User;
use EM\FoundationBundle\Model\UserModel;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;

/**
 * @since 17.3
 */
class MockFactory
{
    public static function getBattlefieldMock(int $size = 7) : Battlefield
    {
        $battlefield = BattlefieldModel::generate($size)
            ->setUser(static::getUserMock(''));

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
        $game   = static::getGameMock($players, $battlefieldSize);
        $result = (new GameResult());
        $game->setResult($result);

        return $result;
    }

    public static function getUserMock(string $email, int $flags = UserModel::FLAG_NONE) : User
    {
        return (new User())
            ->setEmail($email)
            ->setPasswordHash(sha1('mockedPassword'))
            ->setFlags($flags);
    }

    public static function getAIUserMock(string $email) : User
    {
        return static::getUserMock($email, UserModel::FLAG_AI_CONTROLLED);
    }
}
