<?php

namespace EM\GameBundle\Security\AuthenticationToken;

use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerSession;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @since 22.0
 */
class PlayerToken extends AbstractToken
{
    /**
     * @var Player
     */
    protected $user;

    public function getCredentials()
    {
        return $this->getUser();
    }

    /**
     * @param Player $player
     */
    public function setUser($player)
    {
        $this->user = $player;
        $this->setAuthenticated(true);
    }

    public function setSession(PlayerSession $session) : self
    {
        $this->setUser($session->getPlayer());

        return $this;
    }
}
