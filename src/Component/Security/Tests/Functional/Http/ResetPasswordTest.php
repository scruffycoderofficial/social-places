<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Tests\Functional\Http
{
    use BeyondCapable\Component\Security\Domain\Entity\User;

    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\DateTime;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Interval;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;

    use BeyondCapable\Component\Security\Domain\Contract\Gateway\UserGateway;

    use Symfony\Component\Uid\Uuid;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

    /**
     * Class ResetPasswordTest
     *
     * @package BeyondCapable\Component\Security\Tests\Http
     */
    final class ResetPasswordTest extends WebTestCase
    {
        public function testIfResetPasswordIsSuccessful(): void
        {
            $client = static::createClient();

            /** @var UserGateway<User> $userGateway */
            $userGateway = $client->getContainer()->get(UserGateway::class);

            /** @var User $user */
            $user = $userGateway->getUserByEmail(EmailAddress::createFromString('user+1@email.com'));

            $user->requestForAForgottenPassword();
            $userGateway->update($user);

            $client->request(
                Request::METHOD_GET,
                sprintf('/security/reset-password/%s', (string) $user->forgottenPasswordToken)
            );

            $this->assertResponseIsSuccessful();

            $client->submitForm('Reset', [
                'reset_password[password]' => 'new_password',
            ]);

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /** @var User $user */
            $user = $userGateway->getUserByEmail(EmailAddress::createFromString('user+1@email.com'));

            $this->assertNull($user->forgottenPasswordToken);
            $this->assertNull($user->forgottenPasswordRequestedAt);
            $this->assertFalse($user->canResetPassword());
        }

        public function testIfResetPasswordWithEmptyPlainPasswordIsFailed(): void
        {
            $client = static::createClient();

            /** @var UserGateway<User> $userGateway */
            $userGateway = $client->getContainer()->get(UserGateway::class);

            /** @var User $user */
            $user = $userGateway->getUserByEmail(EmailAddress::createFromString('user+1@email.com'));

            $user->requestForAForgottenPassword();
            $userGateway->update($user);

            $client->request(
                Request::METHOD_GET,
                sprintf('/security/reset-password/%s', (string) $user->forgottenPasswordToken)
            );

            $this->assertResponseIsSuccessful();

            $client->submitForm('Reset', [
                'reset_password[password]' => '',
            ]);

            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        }

        public function testIfForgottenPasswordRequestIsExpired(): void
        {
            $client = static::createClient();

            /** @var UserGateway<User> $userGateway */
            $userGateway = $client->getContainer()->get(UserGateway::class);

            /** @var User $user */
            $user = $userGateway->getUserByEmail(EmailAddress::createFromString('user+1@email.com'));

            $user->requestForAForgottenPassword();
            /** @var DateTime $date */
            $date = $user->forgottenPasswordRequestedAt;
            $user->forgottenPasswordRequestedAt = $date->sub(Interval::createFromString(('P2D')));
            $userGateway->update($user);

            $client->request(
                Request::METHOD_GET,
                sprintf('/security/reset-password/%s', (string) $user->forgottenPasswordToken)
            );

            $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        }

        public function testIfForgottenPasswordTokenDoesNotExist(): void
        {
            $client = static::createClient();

            $client->request(
                Request::METHOD_GET,
                sprintf('/security/reset-password/%s', (string) Uuid::v4())
            );

            $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
