<?php

namespace EM\GameBundle\EventListener;

use EM\GameBundle\Model\PlayerSessionModel;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

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

            try {
                $session = $this->model->find($token);
                $request->getSession()->set('_security_main', $session);
            } catch (BadCredentialsException $e) {
                $request->getSession()->set('_security_main', null);
            }
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
