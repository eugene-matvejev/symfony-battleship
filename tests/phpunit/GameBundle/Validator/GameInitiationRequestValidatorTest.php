<?php

namespace EM\Tests\PHPUnit\GameBundle\Validator;

use EM\GameBundle\Validator\GameInitiationRequestValidator;
use EM\Tests\Environment\AbstractKernelTestSuite;
use Symfony\Component\Finder\Finder;

/**
 * @see GameInitiationRequestValidator
 */
class GameInitiationRequestValidatorTest extends AbstractKernelTestSuite
{
    /**
     * @var GameInitiationRequestValidator
     */
    private static $validator;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$validator = static::$container->get('em.game_bundle.validator.game_initiation_request');
    }

    public function validFixturesProvider() : array
    {
        return $this->fixturesProvider('/game-initiation-requests/valid');
    }

    /**
     * @see          GameInitiationRequestValidator::validate
     * @test
     *
     * @dataProvider validFixturesProvider
     *
     * @param string $fileName
     * @param string $content
     */
    public function validateOnValidFixture(string $fileName, string $content)
    {
        $this->assertTrue(
            static::$validator->validate($content),
            "fail to return true by validating {$fileName} fixture"
        );
    }

    public function invalidFixturesProvider() : array
    {
        return $this->fixturesProvider('/game-initiation-requests/invalid');
    }

    /**
     * @see          GameInitiationRequestValidator::validate
     * @test
     *
     * @dataProvider invalidFixturesProvider
     *
     * @param string $fileName
     * @param string $content
     */
    public function validateOnInvalidFixture(string $fileName, string $content)
    {
        $this->assertFalse(
            static::$validator->validate($content),
            "fail to return false by validating {$fileName} fixture"
        );
    }

    private function fixturesProvider(string $path) : array
    {
        $finder = new Finder();
        $finder->files()->in("{$this->getSharedFixturesDirectory()}/$path");

        $arr = [];
        foreach ($finder as $file) {
            $arr[] = [$file->getRealPath(), $file->getContents()];
        }

        return $arr;
    }
}
