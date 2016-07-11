<?php

namespace EM\GameBundle\Security\Firewall;

use EM\GameBundle\Model\PlayerSessionModel;
use EM\GameBundle\Security\Authentication\PlayerToken;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @since 22.0
 */
class PlayerAuthorizationListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $storage;
    /**
     * @var AuthenticationManagerInterface
     */
    protected $manager;
    /**
     * @var PlayerSessionModel
     */
    protected $model;

    public function __construct(TokenStorageInterface $storage, AuthenticationManagerInterface $manager, PlayerSessionModel $model)
    {
        $this->storage = $storage;
        $this->manager = $manager;
        $this->model = $model;
    }

    public function handle(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->headers->has(PlayerSessionModel::AUTHORIZATION_HEADER)) {
            $request->getSession()->set('_security_main', null);
        } else {
            $sessionHash = $request->headers->get(PlayerSessionModel::AUTHORIZATION_HEADER);

            $session = $this->model->find($sessionHash);
            $request->getSession()->set('_security_main', $session);

            $token = (new PlayerToken(['PLAYER']))
                ->setSession($session);

            $this->storage->setToken($token);
        }
    }
}
