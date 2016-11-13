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
    public function parseProvider() : array
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
     * @dataProvider parseProvider
     *
     * @param string $content
     */
    public function parse(string $content)
    {
        $expected = json_decode($content);
        $request  = new GameInitiationRequest($content);

        $this->assertCount(count($expected->coordinates), $request->getCoordinates());
        $this->assertEquals($expected->size, $request->getSize());
        $this->assertEquals($expected->opponents, $request->getOpponents());
    }
}
