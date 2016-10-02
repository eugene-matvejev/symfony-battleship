<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use EM\Tests\Behat\CommonControllerContext;

/**
 * @see GameResultController
 */
class GameResultControllerContext extends CommonControllerContext implements Context, SnippetAcceptingContext
{
    /**
     * @Then observe :expectedAmount results in response
     *
     * @param int $expectedAmount
     */
    public function observeResultsInPage(int $expectedAmount)
    {
        $response = json_decode(self::$_client->getResponse()->getContent());

        $this->assertInstanceOf(\stdClass::class, $response->meta);
        $this->assertCount($expectedAmount, $response->results);
    }
}
