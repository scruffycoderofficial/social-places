<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Request\ParamConverter;

use BeyondCapable\Component\Security\Domain\Entity\User;
use BeyondCapable\Platform\Domain\ValueObject\Token\UuidToken;
use BeyondCapable\Component\Security\Domain\Contract\Gateway\UserGateway;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

use Symfony\Component\HttpFoundation\Request;

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
