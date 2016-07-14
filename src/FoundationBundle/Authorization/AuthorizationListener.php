<?php

namespace EM\FoundationBundle\Authorization;

use EM\FoundationBundle\Authorization\Token\PlayerSessionToken;
use EM\GameBundle\Model\PlayerSessionModel;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @since 22.0
 */
class AuthorizationListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $storage;
    /**
     * @var PlayerSessionModel
     */
    protected $model;

    public function __construct(TokenStorageInterface $storage, PlayerSessionModel $model)
    {
        $this->storage = $storage;
        $this->model = $model;
    }

    public function handle(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->headers->has(PlayerSessionModel::AUTHORIZATION_HEADER)) {
//            $request->getSession()->set('_security_main', null);
//        } else {
            $sessionHash = $request->headers->get(PlayerSessionModel::AUTHORIZATION_HEADER);

            $session = $this->model->find($sessionHash);
//            $request->getSession()->set('_security_main', $session);

            $token = (new PlayerSessionToken(['PLAYER']))
                ->setSession($session);

            $this->storage->setToken($token);
        }
    }
}
