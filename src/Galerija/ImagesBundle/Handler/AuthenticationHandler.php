<?php

namespace Galerija\ImagesBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
/*
 * Klasė skirta nukreipti prisijungusi į prieš tai busvusį puslapį
 */
class AuthenticationHandler implements AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{
    /**
     * Randa klaidas ir grąžina vartotoją į prieš tai buvusį puslapį
     *
     * @param Request $request užklausa
     * @param  AuthenticationException $exception klaida
     * @return RedirectResponse paskutinis puslapi
    */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $referer = $request->headers->get('referer');
        if($exception->getMessageKey() == "Invalid credentials.")
            $request->getSession()->getFlashBag()->add('error',"Netinkami duomenys.");
        else
            $request->getSession()->getFlashBag()->add('error',"Nepavyko prisijungti.");
        return new RedirectResponse($referer);
    }

    /**
     * Nusiunčia vartotoją į paskutinį puslapį
     *
     * @param Request $request užklausa
     * @return RedirectResponse paskutinis puslapis
     */
    public function onLogoutSuccess(Request $request)
    {
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
}