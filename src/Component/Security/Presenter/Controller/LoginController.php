<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Controller
{
    use BeyondCapable\Component\Security\Presenter\ViewModel\LoginViewModel;
    use BeyondCapable\Component\Security\Presenter\Responder\Login\LoginResponderInterface;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

    /**
     * Class LoginController
     *
     * @package App\Security\UserInterface\Controller
     */
    final class LoginController
    {
        public function __invoke(AuthenticationUtils $authenticationUtils, LoginResponderInterface $responder): Response
        {
            return $responder->send(
                new LoginViewModel(
                    $authenticationUtils->getLastUsername(),
                    $authenticationUtils->getLastAuthenticationError()
                )
            );
        }
    }
}
