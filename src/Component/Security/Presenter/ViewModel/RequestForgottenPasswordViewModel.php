<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\ViewModel
{
    use BeyondCapable\Core\Platform\Presenter\ViewModel\ViewModelInterface;

    use Symfony\Component\Form\FormView;
    use Symfony\Component\Form\FormInterface;

    /**
     * Class RequestForgottenPasswordViewModel
     *
     * @package BeyondCapable\Component\Security\Presenter\ViewModel
     */
    final class RequestForgottenPasswordViewModel implements ViewModelInterface
    {
        public FormView $form;

        public function __construct(FormInterface $form)
        {
            $this->form = $form->createView();
        }
    }
}
