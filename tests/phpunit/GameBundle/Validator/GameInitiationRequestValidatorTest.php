<?php

namespace EM\Tests\PHPUnit\GameBundle\Validator;

use EM\GameBundle\Validator\GameInitiationRequestValidator;
use EM\Tests\Environment\IntegrationTestSuite;
use JMS\Serializer\Annotation as Serializer;

/**
 * @see GameInitiationRequestValidator
 */
class GameInitiationRequestValidatorTest extends IntegrationTestSuite
{
    /**
     * @var GameInitiationRequestValidator
     */
    private static $validator;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$validator = static::$container->get('battleship_game.validator.game_initiation_request');
    }

    /**
     * @see GameInitiationRequestValidator::validate
     *
     * @test
     */
    public function validateOnValid()
    {
        $this->assertTrue(static::$validator->validate($this->getSharedFixtureContent('init-game-request-2-players-7x7.json')));
    }

    /**
     * @see GameInitiationRequestValidator::validate
     *
     * @test
     */
    public function validateOnInvalid()
    {
        foreach (static::getListOfInvalidFixtureNames() as $fixtureName) {
            $this->assertFalse(
                static::$validator->validate($this->getInvalidGameRequestFixtureContent($fixtureName)),
                "fail turn return false by validating {$fixtureName} fixture"
            );
        }
    }

    /**
     * @return string[]
     */
    protected function getListOfInvalidFixtureNames() : array
    {
        $fixtures = scandir(static::getSharedFixturesDirectory() . '/invalid-game-initiation-requests');
        /** because 0 and 1 elements contains "." / ".." */
        unset($fixtures[0], $fixtures[1]);

        return $fixtures;
    }

    protected function getInvalidGameRequestFixtureContent(string $name) : string
    {
        return $this->getSharedFixtureContent("invalid-game-initiation-requests/$name");
    }
}
