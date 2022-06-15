<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Controller
{
    use Exception;

    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Core\Voter\UserVoter;
    use BeyondCapable\Component\Security\Presenter\Form\ResetPasswordType;
    use BeyondCapable\Component\Security\Presenter\Input\ResetPasswordInput;
    use BeyondCapable\Component\Security\Presenter\ViewModel\ResetPasswordViewModel;
    use BeyondCapable\Component\Security\Domain\UseCase\ResetPassword\ResetPasswordInterface;
    use BeyondCapable\Component\Security\Presenter\Responder\ResetPassword\ResetPasswordResponderInterface;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Form\FormFactoryInterface;
    use Symfony\Component\HttpFoundation\Exception\BadRequestException;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

    /**
     * Class ResetPasswordController
     *
     * @package BeyondCapable\Component\Security\Presenter\Controller
     */
    final class ResetPasswordController
    {
        public function __invoke(
            User $user,
            Request $request,
            AuthorizationCheckerInterface $authorizationChecker,
            FormFactoryInterface $formFactory,
            ResetPasswordResponderInterface $responder,
            ResetPasswordInterface $useCase,
        ): Response {
            if (!$authorizationChecker->isGranted(UserVoter::CAN_RESET_PASSWORD, $user)) {
                throw new BadRequestException('Token invalid.');
            }

            $input = new ResetPasswordInput($user);
            $form = $formFactory->create(ResetPasswordType::class, $input)->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $useCase($input);

                    return $responder->redirect();
                } catch (Exception) {
                    throw new BadRequestException('Token invalid.');
                }
            }

            return $responder->send(new ResetPasswordViewModel($form));
        }
    }
}
