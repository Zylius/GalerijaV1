<?php

namespace Galerija\ImagesBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationHandler implements AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $referer = $request->headers->get('referer');
        if($exception->getMessageKey() == "Invalid credentials.")
            $request->getSession()->getFlashBag()->add('error',"Netinkami duomenys.");
        else
            $request->getSession()->getFlashBag()->add('error',"Nepavyko prisijungti.");
        return new RedirectResponse($referer);
    }

    public function onLogoutSuccess(Request $request)
    {
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
}