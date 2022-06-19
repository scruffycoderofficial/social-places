<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Persistence\Doctrine\Repository
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\Contract\Gateway\UserGateway;

    use BeyondCapable\Core\Platform\Domain\ValueObject\Token\UuidToken;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;

    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @template-extends ServiceEntityRepository<User>
     *
     * @template-implements UserGateway<User>
     */
    final class UserRepository extends ServiceEntityRepository implements UserGateway
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, User::class);
        }

        public function getUserByEmail(EmailAddress $email): ?User
        {
            return $this->findOneBy(['email' => $email]);
        }

        public function getUserByForgottenPasswordToken(UuidToken $token): ?User
        {
            return $this->findOneBy(['forgottenPasswordToken' => $token]);
        }

        public function update(User $user): void
        {
            $this->_em->flush($user);
        }
    }
}
