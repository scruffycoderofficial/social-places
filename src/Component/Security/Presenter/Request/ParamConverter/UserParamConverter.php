<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Request\ParamConverter
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\Contract\Gateway\UserGateway;

    use BeyondCapable\Core\Platform\Domain\ValueObject\Token\UuidToken;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

    use Symfony\Component\HttpFoundation\Request;

    /**
     * Class UserParamConverter
     *
     * @package BeyondCapable\Component\Security\Presenter\Request\ParamConverter
     */
    final class UserParamConverter implements ParamConverterInterface
    {
        /**
         * @param UserGateway<User> $userGateway
         */
        public function __construct(private UserGateway $userGateway)
        {
        }

        public function apply(Request $request, ParamConverter $configuration): bool
        {
            if ($request->attributes->has('token')) {
                /** @var string $token */
                $token = $request->attributes->get('token');

                $token = UuidToken::createFromString($token);

                $user = $this->userGateway->getUserByForgottenPasswordToken($token);

                if (null === $user) {
                    return false;
                }

                $request->attributes->set($configuration->getName(), $user);
            }

            return true;
        }

        public function supports(ParamConverter $configuration): bool
        {
            return User::class === $configuration->getClass();
        }
    }
}
