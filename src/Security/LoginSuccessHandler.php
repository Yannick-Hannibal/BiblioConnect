<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $roles = $token->getRoleNames(); // Symfony 5.3+ méthode
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $route = 'app_profil';
        } elseif (in_array('ROLE_USER', $roles, true)) {
            $route = 'app_profil';
        }  elseif (in_array('ROLE_USER', $roles, true)) {
            $route = 'app_profil';
        } else {
            $route = 'app_login';
        }

        return new RedirectResponse($this->router->generate($route));
    }
}
