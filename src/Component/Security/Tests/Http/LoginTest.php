<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Tests\Http
{
    use Generator;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

    /**
     * Class LoginTest
     *
     * @package BeyondCapable\Component\Security\Persistence\Tests\Http
     */
    final class LoginTest extends WebTestCase
    {
        public function testIfLoginAfterHasBeenRedirectIsSuccessful(): void
        {
            $client = static::createClient();

            $client->request(Request::METHOD_GET, '/');

            $client->followRedirect();

            $client->submitForm('Sign in', [
                'email' => 'user+1@email.com',
                'password' => 'password',
            ]);

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /** @var AuthorizationCheckerInterface $authorizationChecker */
            $authorizationChecker = $client->getContainer()->get(AuthorizationCheckerInterface::class);

            $this->assertTrue($authorizationChecker->isGranted('ROLE_USER'));
        }

        public function testIfLoginIsSuccessful(): void
        {
            $client = static::createClient();

            $client->request(Request::METHOD_GET, '/security/login');

            $client->submitForm('Sign in', [
                'email' => 'user+1@email.com',
                'password' => 'password',
            ]);

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /** @var AuthorizationCheckerInterface $authorizationChecker */
            $authorizationChecker = $client->getContainer()->get(AuthorizationCheckerInterface::class);

            $this->assertTrue($authorizationChecker->isGranted('ROLE_USER'));
        }

        public function testIfLoginIsFailedWhenUserAccountHasExpired(): void
        {
            $client = static::createClient();

            $client->request(Request::METHOD_GET, '/security/login');

            $client->submitForm('Sign in', [
                'email' => 'user+2@email.com',
                'password' => 'password',
            ]);

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            $crawler = $client->followRedirect();

            $this->assertStringContainsString('Account has expired.', $crawler->filter('div.error')->text());
        }

        public function testIfLoginIsFailedWhenUserIsSuspended(): void
        {
            $client = static::createClient();

            $client->request(Request::METHOD_GET, '/security/login');

            $client->submitForm('Sign in', [
                'email' => 'user+3@email.com',
                'password' => 'password',
            ]);

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            $crawler = $client->followRedirect();

            $this->assertStringContainsString('Your user account has been suspended.', $crawler->filter('div.error')->text());
        }

        /**
         * @param array{email: string, password: string} $formData
         *
         * @dataProvider provideBadData
         */
        public function testIfLoginIsFailed(array $formData): void
        {
            $client = static::createClient();

            $client->request(Request::METHOD_GET, '/security/login');

            $client->submitForm('Sign in', $formData);

            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            /** @var AuthorizationCheckerInterface $authorizationChecker */
            $authorizationChecker = $client->getContainer()->get(AuthorizationCheckerInterface::class);

            $this->assertFalse($authorizationChecker->isGranted('ROLE_USER'));

            $crawler = $client->followRedirect();

            $this->assertStringContainsString('Invalid credentials.', $crawler->filter('div.error')->text());
        }

        /**
         * @return Generator<string, array<array-key, mixed>>
         */
        public function provideBadData(): Generator
        {
            /**
             * @param array{email: ?string, password: ?string} $data
             *
             * @return array{email: string, password: string}
             */
            $baseData = static fn (array $data): array => $data + [
                    'email' => 'user+1@email.com',
                    'password' => 'password',
                ];

            yield 'password is wrong' => [$baseData(['password' => 'fail'])];
            yield 'email is wrong' => [$baseData(['email' => 'fail'])];
        }
    }
}
