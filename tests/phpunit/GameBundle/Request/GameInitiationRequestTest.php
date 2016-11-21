<?php

namespace EM\Tests\PHPUnit\GameBundle\Request;

use EM\GameBundle\Request\GameInitiationRequest;
use EM\Tests\Environment\AbstractKernelTestSuite;
use Symfony\Component\Finder\Finder;

/**
 * @see GameInitiationRequest
 */
class GameInitiationRequestTest extends AbstractKernelTestSuite
{
    public function parseDataProvider() : array
    {
        $suites = [];
        $finder = new Finder();
        $finder->files()->in("{$this->getSharedFixturesDirectory()}/game-initiation-requests/valid");

        foreach ($finder as $file) {
            $suites[$file->getFilename()] = [$file->getContents()];
        }

        return $suites;
    }

    /**
     * @see          GameInitiationRequest::parse
     * @test
     *
     * @dataProvider parseDataProvider
     *
     * @param string $content
     */
    public function parse(string $content)
    {
        $json    = json_decode($content);
        $request = new GameInitiationRequest($content);

        $this->assertEquals($json->size, $request->getSize());
        $this->assertEquals($json->opponents, $request->getOpponents());
        $this->assertEquals($json->coordinates, $request->getCoordinates());
    }
}
