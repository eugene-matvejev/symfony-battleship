<?php

namespace EM\Tests\PHPUnit\GameBundle\AI;

use EM\GameBundle\AI\AI;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see EM\GameBundle\AI\AI
 */
class AITest extends ExtendedTestCase
{
    /**
     * @var AI
     */
    private $ai;

    protected function setUp()
    {
        parent::setUp();
        $this->ai =  parent::getContainer()->get('battleship.game.services.ai.core');
    }

    /**
     * @see EM\GameBundle\AI\AI::chooseCellToAttack
     * @test
     */
    public function chooseCellToAttack()
    {
        $reflected = new \ReflectionClass(AI::class);
        $method = $reflected->getMethod(__FUNCTION__);
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs($this->ai, ['cells' => []]));
    }

    /**
     *
     */
    protected function attackCell()
    {
    }
}