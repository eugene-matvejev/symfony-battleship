<?php

namespace EM\GameBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class PlayerAuthorizationListener
{
    const AUTHORIZATION_HEADER = 'x-wsse';

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
            $request->getSession()->set('_security_main', $token);
        }
//        if (!$this->isWeb($request)) {
//            return;
//        }
        // Emulate session exist. Need for \Symfony\Component\Security\Http\Firewall\ContextListener for first time site open
//        if (!$request->hasPreviousSession()) {
//            $request->cookies->set($request->getSession()->getName(), 'emulate');
//        }

//        $token = $event->getRequest()->cookies->get($this->cookieKey);
////        $token = $this->getTokenHash($token);
//        if()
//        if (empty($token)) {
//            $request->getSession()->set('_security_main', null);
//        } else {
//            $user = (new User())
//                ->setRubyToken($token)
//                ->setTimezoneFromRequest($event->getRequest());
//            $token = new UsernamePasswordToken($user, null, 'main');
//            $request->getSession()->set('_security_main', serialize($token));
//        }
    }
}
