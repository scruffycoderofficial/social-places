<?php

declare(strict_types=1);

namespace BeyondCapable\Component\Security\Presenter\Form
{
    use BeyondCapable\Component\Security\Presenter\Input\RequestForgottenPasswordInput;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    /**
     * Class RequestForgottenPasswordType
     *
     * @package BeyondCapable\Component\Security\Presenter\Form
     */
    final class RequestForgottenPasswordType extends AbstractType
    {
        /**
         * @param array<string, int|bool|string|null> $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('email', EmailType::class, ['empty_data' => '']);
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefault('data_class', RequestForgottenPasswordInput::class);
        }
    }
}
