<?php

namespace EM\GameBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class PlayerAuthorizationListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
    }
}
