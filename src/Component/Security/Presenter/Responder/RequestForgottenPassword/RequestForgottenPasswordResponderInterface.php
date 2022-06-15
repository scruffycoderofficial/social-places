<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Responder\RequestForgottenPassword
{
    use BeyondCapable\Component\Security\Presenter\ViewModel\RequestForgottenPasswordViewModel;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\RedirectResponse;

    /**
     * Interface RequestForgottenPasswordResponderInterface
     *
     * @package BeyondCapable\Component\Security\Presenter\Responder\RequestForgottenPassword
     */
    interface RequestForgottenPasswordResponderInterface
    {
        public function send(RequestForgottenPasswordViewModel $viewModel): Response;

        public function redirect(): RedirectResponse;
    }
}
