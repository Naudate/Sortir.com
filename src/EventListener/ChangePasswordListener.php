<?php
// src/EventListener/FirstConnectionListener.php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class ChangePasswordListener implements EventSubscriberInterface
{
    private $router;
    private $security;

    public function __construct(RouterInterface $router, Security $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $route = $event->getRequest()->get('_route');

        // Vérifier si l'utilisateur est connecté
        $user = $this->security->getUser();
        if ($user) {

            if (!$user->isIsChangePassword()){
                return;
            }

            // Vérifier si l'attribut firstConnection est à true
            if ($user->isIsChangePassword()) {
                // Rediriger vers la page firstConnection
                if($route == 'user_changePassword'){
                    return;
                }else{
                    $response = new RedirectResponse($this->router->generate('user_changePassword', array('id'=> $user->getId())));
                }


            }else{
                if($route == 'user_changePassword'){
                    $response = new RedirectResponse($this->router->generate('app_home'));
                }else{
                    return;
                }

            }

            $event->setResponse($response);

            if ($user->getRoles() != ["ROLE_USER"]){
                return;
            }


        }
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}
