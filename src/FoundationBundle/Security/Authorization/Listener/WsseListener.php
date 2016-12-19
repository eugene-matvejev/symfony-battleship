<?php

namespace EM\FoundationBundle\Security\Authorization\Listener;

use EM\FoundationBundle\Security\Authorization\Token\WsseToken;
use EM\GameBundle\Model\UserSessionModel;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @since 23.0
 */
class WsseListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $storage;
    /**
     * @var UserSessionModel
     */
    private $model;

    public function __construct(TokenStorageInterface $storage, UserSessionModel $model)
    {
        $this->storage = $storage;
        $this->model = $model;
    }

    public function handle(GetResponseEvent $event) : bool
    {
        if ($event->isMasterRequest() && $event->getRequest()->headers->get(UserSessionModel::SESSION_HEADER)) {
            $sessionHash = $event->getRequest()->headers->get(UserSessionModel::SESSION_HEADER);

            $session = $this->model->find($sessionHash);

            $token = (new WsseToken(['PLAYER']))
                ->setSession($session);

            $this->storage->setToken($token);
        }

        return false;
    }
}
