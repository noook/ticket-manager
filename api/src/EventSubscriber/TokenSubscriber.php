<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use App\Service\TokenHandler;

class TokenSubscriber implements EventSubscriberInterface
{
    private $tokenHandler;

    public function __construct(TokenHandler $tokenHandler)
    {
        $this->tokenHandler = $tokenHandler;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $oldToken = $request->headers->get('X-AUTH-TOKEN');
        
        $newToken = $this->tokenHandler->refreshToken($oldToken);
        $response = $event->getResponse();

        $response->headers->set('X-ACCESS-TOKEN', $newToken);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }
}