<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\ValueObject\Email
{
    use BeyondCapable\Core\Platform\Domain\Exception\InvalidArgumentException;

    use Stringable;

    use Symfony\Component\Validator\Validation;
    use Symfony\Component\Validator\Constraints\Email;

    /**
     * Class EmailAddress
     *
     * @package BeyondCapable\Core\Platform\Domain\ValueObject\Email
     */
    class EmailAddress implements Stringable
    {
        /**
         * EmailAddress constructor.
         *
         * @param string $email
         * @throws InvalidArgumentException
         */
        private function __construct(private string $email)
        {
            if (Validation::createValidator()->validate($this->email, [new Email()])->count() > 0) {
                throw new InvalidArgumentException(sprintf('%s is not a valid email', $this->email));
            }
        }

        /**
         * @return string
         */
        public function __toString(): string
        {
            return $this->email;
        }

        /**
         * @param string $email
         * @return EmailAddress
         * @throws InvalidArgumentException
         */
        public static function createFromString(string $email): EmailAddress
        {
            return new EmailAddress($email);
        }

        /**
         * @param EmailAddress $email
         * @return bool
         */
        public function equalTo(EmailAddress $email): bool
        {
            return (string) $email === (string) $this;
        }
    }
}
