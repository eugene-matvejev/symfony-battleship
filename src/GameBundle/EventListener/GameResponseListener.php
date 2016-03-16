<?php

namespace EM\GameBundle\EventListener;

use EM\GameBundle\Response\GameResponseInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class GameResponseListener
{
    /**
     * @var
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelRequest(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if ($response instanceof GameResponseInterface) {
            $accept = $event->getRequest()->headers->get('accept');

            if (false !== strpos($accept, 'application/xml')) {
                $serialized = $this->serializer->serialize($response->getContent(), 'xml');
            } else if (false !== strpos($accept, 'application/json')) {
                $serialized = $this->serializer->serialize($response->getContent(), 'json');
            } else {
                $serialized = $response->getContent();
            }

            $response->setContent($serialized);
        }
    }
}
