<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Responder\ResetPassword
{
    use BeyondCapable\Core\Platform\Presenter\Responder\TwigResponder;
    use BeyondCapable\Component\Security\Presenter\ViewModel\ResetPasswordViewModel;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    /**
     * Class ResetPasswordResponder
     *
     * @package BeyondCapable\Component\Security\Presenter\Responder\ResetPassword
     */
    final class ResetPasswordResponder implements ResetPasswordResponderInterface
    {
        public function __construct(private TwigResponder $decorated, private UrlGeneratorInterface $urlGenerator)
        {
        }

        public function send(ResetPasswordViewModel $viewModel): Response
        {
            return $this->decorated->send('@security/reset_password.html.twig', $viewModel);
        }

        public function redirect(): RedirectResponse
        {
            return new RedirectResponse($this->urlGenerator->generate('security_login'));
        }
    }
}
