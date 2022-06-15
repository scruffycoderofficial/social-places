<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\ViewModel
{
    use BeyondCapable\Platform\Presenter\ViewModel\ViewModelInterface;

    use Symfony\Component\Security\Core\Exception\AuthenticationException;

    /**
     * Class LoginViewModel
     *
     * @package BeyondCapable\Component\Security\Presenter\ViewModel
     */
    final class LoginViewModel implements ViewModelInterface
    {
        public function __construct(public string $lastUsername, public ?AuthenticationException $error)
        {
        }
    }
}
