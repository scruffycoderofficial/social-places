<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Responder\Login
{
    use BeyondCapable\Component\Security\Presenter\ViewModel\LoginViewModel;

    use Symfony\Component\HttpFoundation\Response;

    /**
     * Interface LoginResponderInterface
     *
     * @package BeyondCapable\Component\Security\Presenter\Responder\Login
     */
    interface LoginResponderInterface
    {
        public function send(LoginViewModel $loginViewModel): Response;
    }
}
