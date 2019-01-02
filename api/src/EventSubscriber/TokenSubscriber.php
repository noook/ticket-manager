<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use App\Service\TokenHandler;
use App\Security\TokenAuthenticator;
use App\Security\UserProvider;

class TokenSubscriber implements EventSubscriberInterface
{
    private $tokenHandler;
    private $tokenAuthenticator;
    private $userProvider;

    public function __construct(TokenHandler $tokenHandler, TokenAuthenticator $tokenAuthenticator, UserProvider $userProvider)
    {
        $this->tokenHandler = $tokenHandler;
        $this->tokenAuthenticator = $tokenAuthenticator;
        $this->userProvider = $userProvider;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $token = $request->headers->get('X-AUTH-TOKEN');
        
        if (is_null($token)) {
            return;
        }

        $credentials = $this->tokenAuthenticator->getCredentials($request);
        $user = $this->tokenAuthenticator->getUser($credentials, $this->userProvider);
        if (is_null($user)) {
            throw new AccessDeniedHttpException('Requires Authentication');
        }
        
        $now = new \DateTime();
        $nearExpiracy = $user->getTokenExpiracy()->sub(new \DateInterval('PT1H'));
        if ($nearExpiracy < $now) {
            $newToken = $this->tokenHandler->refreshToken($token);
            $response->headers->set('X-REFRESHED-TOKEN', $newToken);
            $response->headers->set('X-TOKEN-EXPIRACY', (new \DateTime())->add(new \DateInterval('P1D'))->format('c'));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }
}