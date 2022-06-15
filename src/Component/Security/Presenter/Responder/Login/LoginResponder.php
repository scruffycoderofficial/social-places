<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Responder\Login
{
    use BeyondCapable\Component\Security\Presenter\Responder\TwigResponder;
    use BeyondCapable\Component\Security\Presenter\ViewModel\LoginViewModel;

    use Symfony\Component\HttpFoundation\Response;

    /**
     * Class LoginResponder
     *
     * @package BeyondCapable\Component\Security\Presenter\Responder\Login]
     */
    final class LoginResponder implements LoginResponderInterface
    {
        public function __construct(private TwigResponder $decorated)
        {
        }

        public function send(LoginViewModel $loginViewModel): Response
        {
            return $this->decorated->send('@security/login.html.twig', $loginViewModel);
        }
    }
}
