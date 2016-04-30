<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use Behat\Behat\Context\SnippetAcceptingContext;
use EM\GameBundle\Controller\GameController;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @see GameController
 */
class GameControllerContext implements SnippetAcceptingContext //extends ExtendedTestCase
{
    public function __construct(Session $session)
    {
    }
}
