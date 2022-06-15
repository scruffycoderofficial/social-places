<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter
{
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordOutput;
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordPresenterInterface;

    use Symfony\Component\Mime\Address;
    use Symfony\Bridge\Twig\Mime\TemplatedEmail;
    use Symfony\Component\Mailer\MailerInterface;

    /**
     * Class RequestForgottenPasswordPresenter
     *
     * @package BeyondCapable\Component\Security\Presenter
     */
    final class RequestForgottenPasswordPresenter implements RequestForgottenPasswordPresenterInterface
    {
        public function __construct(private MailerInterface $mailer, private string $emailNoReply)
        {
        }

        public function present(RequestForgottenPasswordOutput $output): void
        {
            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address($this->emailNoReply))
                    ->to(new Address((string) $output->user->email))
                    ->subject('Your forgotten password request')
                    ->htmlTemplate('@security/emails/request_forgotten_password.html.twig')
                    ->context(['user' => $output->user])
            );
        }
    }
}
