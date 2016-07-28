<?php

namespace EM\FoundationBundle\Authorization;

use EM\FoundationBundle\Authorization\Token\PlayerSessionToken;
use EM\GameBundle\Model\PlayerSessionModel;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @since 23.0
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

    public function handle(GetResponseEvent $event) : bool
    {
        if (!$event->isMasterRequest() || !$event->getRequest()->headers->has(PlayerSessionModel::SESSION_HEADER)) {
            $sessionHash = $event->getRequest()->headers->get(PlayerSessionModel::SESSION_HEADER);

            $session = $this->model->find($sessionHash);

            $token = (new PlayerSessionToken(['PLAYER']))
                ->setSession($session);

            $this->storage->setToken($token);
        }

        return false;
    }
}
