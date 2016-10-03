<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use EM\Tests\Behat\CommonControllerContext;

/**
 * @see GameResultController
 */
class GameResultControllerContext extends CommonControllerContext
{
    /**
     * @Then observe :expectedAmount results in response
     *
     * @param int $expectedAmount
     */
    public function observeResultsInPage(int $expectedAmount)
    {
        $response = json_decode(self::$client->getResponse()->getContent());

        $this->assertInstanceOf(\stdClass::class, $response->meta);
        $this->assertCount($expectedAmount, $response->results);
    }
}
