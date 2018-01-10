<?php

namespace EM\FoundationBundle\Security\Authorization\Token;

use EM\FoundationBundle\Entity\User;
use EM\FoundationBundle\Entity\UserSession;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @since 23.0
 */
class WsseToken extends AbstractToken
{
    /**
     * @var User
     */
    protected $user;
    /**
     * @var UserSession
     */
    private $session;

    public function getCredentials()
    {
        return $this->getUser();
    }

    public function getSession() : UserSession
    {
        return $this->session;
    }

    public function setSession(UserSession $session) : self
    {
        $this->session = $session;
        $this->setUser($session->getUser());

        return $this;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        $this->setAuthenticated(true);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
