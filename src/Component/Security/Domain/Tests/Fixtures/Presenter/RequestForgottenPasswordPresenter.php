<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Presenter
{
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordOutput;
    use BeyondCapable\Component\Security\Domain\UseCase\RequestForgottenPassword\RequestForgottenPasswordPresenterInterface;

    /**
     * Class RequestForgottenPasswordPresenter
     *
     * @package BeyondCapable\Component\Security\Domain\Tests\Fixtures\UserInterface\Presenter
     */
    final class RequestForgottenPasswordPresenter implements RequestForgottenPasswordPresenterInterface
    {
        public RequestForgottenPasswordOutput $output;

        public function present(RequestForgottenPasswordOutput $output): void
        {
            $this->output = $output;
        }
    }
}
