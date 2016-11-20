<?php

namespace EM\FoundationBundle\Authorization\Token;

use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerSession;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @since 23.0
 *
 * @method Player getUser()
 */
class PlayerSessionToken extends AbstractToken
{
    /**
     * @var Player
     */
    private $user;
    /**
     * @var PlayerSession
     */
    private $session;

    public function getCredentials()
    {
        return $this->getUser();
    }

    public function getSession() : PlayerSession
    {
        return $this->session;
    }

    public function setSession(PlayerSession $session) : self
    {
        $this->session = $session;
        $this->setUser($session->getPlayer());

        return $this;
    }

    /**
     * @param Player $player
     */
    public function setUser($player)
    {
        $this->user = $player;
        $this->setAuthenticated(true);
    }
}
