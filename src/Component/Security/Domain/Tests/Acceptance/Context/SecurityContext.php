<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Acceptance\Context
{
    use BeyondCapable\Component\Security\Domain\Entity\User;
    use BeyondCapable\Component\Security\Domain\UseCase\ResetPassword\ResetPassword;
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPassword;
    use BeyondCapable\Component\Security\Domain\Tests\Fixtures\Domain\Repository\UserRepository;
    use BeyondCapable\Component\Security\Domain\Tests\Fixtures\Presenter\Input\ResetPasswordInput;
    use BeyondCapable\Component\Security\Domain\Tests\Fixtures\Presenter\Input\RequestForgottenPasswordInput;
    use BeyondCapable\Component\Security\Domain\Tests\Fixtures\Presenter\RequestForgottenPasswordPresenter;
    use BeyondCapable\Component\Security\Domain\Tests\Fixtures\Core\PasswordHasher;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\PlainPassword;
    use BeyondCapable\Component\Security\Domain\ValueObject\Password\HashedPassword;

    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\DateTime;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Date\Interval;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Email\EmailAddress;
    use BeyondCapable\Core\Platform\Domain\ValueObject\Identifier\UuidIdentifier;

    use Exception;
    use Behat\Behat\Context\Context;

    use PHPUnit\Framework\Assert;
    use Symfony\Component\HttpKernel\KernelInterface;

    /**
     * Class SecurityContext
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Acceptance\Context
     */
    final class SecurityContext implements Context
    {

        /** @var KernelInterface */
        private $kernel;

        private $methodCallName;

        private User $registeredUser;

        private string $email;

        private string $plainPassword;

        private \Closure $callback;

        private PasswordHasher $passwordHasher;

        public function __construct(KernelInterface $kernel)
        {
            $this->kernel = $kernel;

            $this->passwordHasher = new PasswordHasher();
        }

        /**
         * @Given /^I registered with my email address (.+)$/
         */
        public function iRegisteredWithMyEmailAddress(string $email): void
        {
            $this->registeredUser = User::create(
                identifier: UuidIdentifier::create(),
                email: EmailAddress::createFromString($email)
            );
        }

        /**
         * @Given /^I have already request a forgotten password (\d+) hours ago$/
         */
        public function iHaveAlreadyRequestAForgottenPasswordHoursAgo(int $hours): void
        {
            $this->registeredUser->requestForAForgottenPassword();
            /** @var DateTime $forgottenPasswordRequestedAt */
            $forgottenPasswordRequestedAt = $this->registeredUser->forgottenPasswordRequestedAt;
            $this->registeredUser->forgottenPasswordRequestedAt = $forgottenPasswordRequestedAt->sub(
                Interval::createFromString(
                    sprintf('PT%dH', $hours)
                )
            );
        }

        /**
         * @Given /^I requested a forgotten password (\d+) hours ago$/
         */
        public function iRequestAForgottenPassword(int $hours): void
        {
            $this->registeredUser->requestForAForgottenPassword();

            /** @var DateTime $date */
            $date = $this->registeredUser->forgottenPasswordRequestedAt;

            $this->registeredUser->forgottenPasswordRequestedAt = $date->sub(
                Interval::createFromString(
                    sprintf('PT%dH', $hours)
                )
            );
        }

        /**
         * @When /^I reset my password with (.+)/
         */
        public function iResetMyPasswordWith(string $plainPassword): void
        {
            $this->plainPassword = $plainPassword;

            $this->callback = function () {
                $userGateway = new UserRepository([$this->registeredUser]);
                $useCase = new ResetPassword($userGateway, $this->passwordHasher);
                $useCase(new ResetPasswordInput($this->plainPassword, $this->registeredUser));
            };
        }

        /**
         * @When /^I request a forgotten password with (.+)$/
         */
        public function iRequestAForgottenPasswordWith(string $email): void
        {
            $this->email = $email;

            $this->callback = function () {
                $userGateway = new UserRepository([$this->registeredUser]);
                $useCase = new RequestForgottenPassword($userGateway);
                $useCase(new RequestForgottenPasswordInput($this->email), new RequestForgottenPasswordPresenter());
            };
        }

        /**
         * @Then /^I can use my forgotten password token for the next 24 hours$/
         */
        public function thenICanUseMyForgottenPasswordTokenForTheNextHours(): void
        {
            $this->callback->call($this);
            Assert::assertNotNull($this->registeredUser->forgottenPasswordRequestedAt);
            Assert::assertNotNull($this->registeredUser->forgottenPasswordToken);
            Assert::assertTrue($this->registeredUser->canResetPassword());
        }

        /**
         * @Then /^My password is reset and I can log in again$/
         */
        public function myPasswordIsResetAndICanLogInAgain(): void
        {
            $this->callback->call($this);
            Assert::assertNull($this->registeredUser->forgottenPasswordRequestedAt);
            Assert::assertNull($this->registeredUser->forgottenPasswordToken);
            Assert::assertNull($this->registeredUser->plainPassword);
            Assert::assertFalse($this->registeredUser->canResetPassword());

            /** @var HashedPassword $hashedPassword */
            $hashedPassword = $this->registeredUser->hashedPassword;
            Assert::assertTrue(
                $hashedPassword->verify(
                    $this->passwordHasher,
                    PlainPassword::createFromString($this->plainPassword)
                )
            );
        }

        /**
         * @Then /^I get an error that tells me "(.+)"/
         */
        public function iGetAnErrorThatMyEmailDoesNotExist(string $errorMessage): void
        {
            try {
                $this->callback->call($this);
            } catch (Exception $exception) {
                Assert::assertEquals($errorMessage, $exception->getMessage());
            }
        }

        /**
         * @When an instance of the Kernel class is loaded
         */
        public function anInstanceOfTheKernelClassIsLoaded()
        {
            return !is_null($this->kernel);
        }

        /**
         * @When the (.+) method is called
         */
        public function theMethodIsCalled(string $methodName)
        {
            $this->methodCallName = $methodName;

            return method_exists($this, $methodName);
        }

        /**
         * @Then the result should be positive
         */
        public function theResultShouldBePositive()
        {
            $methodCallName = $this->methodCallName;

            return is_array($this->kernel->{$methodCallName}());
        }
    }
}
