<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Responder\ResetPassword
{
    use BeyondCapable\Component\Security\Presenter\ViewModel\ResetPasswordViewModel;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\RedirectResponse;

    /**
     * Interface ResetPasswordResponderInterface
     *
     * @package BeyondCapable\Component\Security\Presenter\Responder\ResetPassword
     */
    interface ResetPasswordResponderInterface
    {
        public function send(ResetPasswordViewModel $viewModel): Response;

        public function redirect(): RedirectResponse;
    }
}
