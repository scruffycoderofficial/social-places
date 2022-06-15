<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Responder\RequestForgottenPassword
{
    use BeyondCapable\Platform\Presenter\Responder\TwigResponder;
    use BeyondCapable\Component\Security\Presenter\ViewModel\RequestForgottenPasswordViewModel;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    /**
     * Class RequestForgottenPasswordResponder
     *
     * @package App\Security\UserInterface\Responder\RequestForgottenPassword
     */
    final class RequestForgottenPasswordResponder implements RequestForgottenPasswordResponderInterface
    {
        public function __construct(private TwigResponder $decorated, private UrlGeneratorInterface $urlGenerator)
        {
        }

        public function send(RequestForgottenPasswordViewModel $viewModel): Response
        {
            return $this->decorated->send('@security/request_forgotten_password.html.twig', $viewModel);
        }

        public function redirect(): RedirectResponse
        {
            return new RedirectResponse($this->urlGenerator->generate('security_login'));
        }
    }
}
