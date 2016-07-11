<?php

namespace EM\GameBundle\EventListener;

use EM\GameBundle\Model\PlayerSessionModel;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class PlayerAuthorizationListener
{
    const AUTHORIZATION_HEADER = 'x-wsse';
    /**
     * @var PlayerSessionModel
     */
    protected $model;

    public function __construct(PlayerSessionModel $model)
    {
        $this->model = $model;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->headers->has(static::AUTHORIZATION_HEADER)) {
            $request->getSession()->set('_security_main', null);
        } else {
            $token = $request->headers->get(static::AUTHORIZATION_HEADER);

            $session = $this->model->find($token);
            $request->getSession()->set('_security_main', $session);
        }
    }
}
