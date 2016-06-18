<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use EM\GameBundle\Controller\GameController;
use EM\Tests\Behat\AbstractContainerAwareContext;

/**
 * @see GameController
 */
class GameControllerContext extends AbstractContainerAwareContext implements Context, SnippetAcceptingContext
{
    /**
     * @Given request GUI :route route via :method method
     *
     * @param string $route
     * @param string $method
     */
    public function requestGuiRouteViaMethod(string $route, string $method)
    {
        $routeParams = [];
        if (!empty($paramKey) && !empty($paramValue)) {
            $routeParams[$paramKey] = $paramValue;
        }

        self::$_client->request(
            $method,
            static::$router->generate($route, $routeParams),
            [],
            [],
            []
        );
    }
}
