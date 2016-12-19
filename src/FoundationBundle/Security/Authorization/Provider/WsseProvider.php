<?php

namespace EM\FoundationBundle\Security\Authorization\Provider;

use EM\FoundationBundle\Security\Authorization\Token\WsseToken;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class WsseProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cachePool;

    public function __construct(UserProviderInterface $userProvider, CacheItemPoolInterface $cachePool)
    {
        $this->userProvider = $userProvider;
        $this->cachePool    = $cachePool;
    }

    public function authenticate(TokenInterface $token)
    {
        throw new AuthenticationException('The WSSE authentication failed.');
    }


    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseToken;
    }
}