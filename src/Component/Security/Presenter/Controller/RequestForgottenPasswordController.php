<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Controller
{
    use BeyondCapable\Component\Security\Presenter\Form\RequestForgottenPasswordType;
    use BeyondCapable\Component\Security\Presenter\Input\RequestForgottenPasswordInput;
    use BeyondCapable\Component\Security\Presenter\ViewModel\RequestForgottenPasswordViewModel;
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordInterface;
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordPresenterInterface;
    use BeyondCapable\Component\Security\Presenter\Responder\RequestForgottenPassword\RequestForgottenPasswordResponderInterface;

    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Symfony\Component\Form\FormError;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Form\FormFactoryInterface;

    /**
     * Class RequestForgottenPasswordController
     *
     * @package BeyondCapable\Component\Security\Presenter\Controller
     */
    final class RequestForgottenPasswordController
    {
        public function __invoke(
            Request $request,
            FormFactoryInterface $formFactory,
            RequestForgottenPasswordResponderInterface $responder,
            RequestForgottenPasswordPresenterInterface $presenter,
            RequestForgottenPasswordInterface $useCase,
        ): Response {
            $input = new RequestForgottenPasswordInput();

            $form = $formFactory->create(RequestForgottenPasswordType::class, $input)->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $useCase($input, $presenter);

                    return $responder->redirect();
                } catch (InvalidArgumentException $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                }
            }

            return $responder->send(new RequestForgottenPasswordViewModel($form));
        }
    }
}
