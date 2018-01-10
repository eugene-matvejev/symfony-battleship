<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\FoundationBundle\DataFixtures\ORM\UsersFixture;
use EM\FoundationBundle\Entity\User;
use EM\FoundationBundle\Model\UserModel;
use EM\Tests\Environment\AbstractKernelTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see UserModel
 */
class UserModelTest extends AbstractKernelTestSuite
{
    /**
     * @var UserModel
     */
    private static $UserModel;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$UserModel = static::$container->get('em.foundation_bundle.model.user');
    }

    public function isAIControlledDataProvider() : array
    {
        return [
            [false, MockFactory::getUserMock('')],
            [true, MockFactory::getAIUserMock('')]
        ];
    }

    /**
     * should return true if player marked by @see UserModel::FLAG_AI_CONTROLLED flag otherwise false
     *
     * @see          UserModel::isAIControlled
     * @test
     *
     * @dataProvider isAIControlledDataProvider
     *
     * @param bool $result
     * @param User $user
     */
    public function isAIControlled(bool $result, User $user)
    {
        $this->assertSame($result, UserModel::isAIControlled($user));
    }

    public function createOnRequestAIControlledDataProvider() : array
    {
        return [
            [UsersFixture::TEST_AI_PLAYER_EMAIL, 'int'],
            [UsersFixture::TEST_AI_PLAYER_EMAIL . 'NON-EXISTS', 'null']
        ];
    }

    /**
     * should return new player controlled by AI, as it didn't exist before
     *
     * @see          UserModel::createOnRequestAIControlled
     * @test
     *
     * @dataProvider createOnRequestAIControlledDataProvider
     *
     * @param string $username
     * @param string $idFieldType
     */
    public function createOnRequestAIControlled(string $username, string $idFieldType)
    {
        $player = static::$UserModel->createOnRequestAIControlled($username);

        $this->assertInternalType($idFieldType, $player->getId());
        $this->assertTrue(UserModel::isAIControlled($player));
        $this->assertSame($username, $player->getEmail());
    }

    public function createPlayerDataProvider() : array
    {
        return [
            ['AI controlled', '', UserModel::FLAG_AI_CONTROLLED],
            ['human controlled', '', UserModel::FLAG_NONE]
        ];
    }

    /**
     * @see          UserModel::createOnRequestHumanControlled
     * @test
     *
     * @dataProvider createPlayerDataProvider
     *
     * @param string $username
     * @param string $password
     * @param int    $flag
     */
    public function createPlayer(string $username, string $password, int $flag)
    {
        $player = static::$UserModel->createPlayer($username, $password, $flag);

        /** because player is not persisted yet */
        $this->assertNull($player->getId());
        $this->assertSame($username, $player->getEmail());
        $this->assertSame($flag, $player->getFlags());
    }
}
