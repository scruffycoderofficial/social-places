<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Unit\UseCase;

use BeyondCapable\Component\Security\Domain\Entity\User;
use BeyondCapable\Component\Security\Domain\Tests\Fixtures\Infrastructure\Repository\UserRepository;
use BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Input\RequestForgottenPasswordInput;
use BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Presenter\RequestForgottenPasswordPresenter;
use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPassword;
use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordInterface;
use BeyondCapable\Platform\Domain\Exception\InvalidArgumentException;
use BeyondCapable\Platform\Domain\ValueObject\Email\EmailAddress;
use BeyondCapable\Platform\Domain\ValueObject\Identifier\UuidIdentifier;

use PHPUnit\Framework\TestCase;

final class RequestForgottenPasswordTest extends TestCase
{
    private UserRepository $userRepository;

    private RequestForgottenPasswordInterface $useCase;

    private RequestForgottenPasswordPresenter $presenter;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository([]);
        $this->useCase = new RequestForgottenPassword($this->userRepository);
        $this->presenter = new RequestForgottenPasswordPresenter();
    }

    public function testIfRequestForgottenPasswordCreateTokenForNext24Hours(): void
    {
        $user = User::create(
            identifier: UuidIdentifier::create(),
            email: EmailAddress::createFromString('user+1@email.com')
        );

        $this->userRepository->users[] = $user;

        $this->useCase->__invoke(new RequestForgottenPasswordInput('user+1@email.com'), $this->presenter);

        $this->assertNotNull($user->forgottenPasswordRequestedAt);
        $this->assertNotNull($user->forgottenPasswordToken);
        $this->assertTrue($user->canResetPassword());
        $this->assertEquals($user, $this->presenter->output->user);
    }

    public function testIfRequestForgottenPasswordWithNonExistingEmailRaiseException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This email does not exist.');
        $this->useCase->__invoke(new RequestForgottenPasswordInput('user+0@email.com'), $this->presenter);
    }

    public function testIfRequestForgottenPasswordWhenIHaveAlreadyMadeARequestRaiseException(): void
    {
        $user = User::create(
            identifier: UuidIdentifier::create(),
            email: EmailAddress::createFromString('user+2@email.com')
        );

        $user->requestForAForgottenPassword();

        $this->userRepository->users[] = $user;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You have already request for a forgotten password last 24 hours.');
        $this->useCase->__invoke(new RequestForgottenPasswordInput('user+2@email.com'), $this->presenter);
    }
}
