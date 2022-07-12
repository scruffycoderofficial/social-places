<?php

namespace Oro\Bundle\AddressBundle\Form\Type;

use Oro\Bundle\TranslationBundle\Form\Type\Select2TranslatableEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionType extends AbstractType
{
    const COUNTRY_OPTION_KEY = 'country_field';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->setAttribute(self::COUNTRY_OPTION_KEY, $options[self::COUNTRY_OPTION_KEY]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
            // show empty list if country is not selected
            if (empty($options['country'])) {
                return array();
            }

            return null;
        };

        $resolver
            ->setDefaults(
                array(
                    'label'         => 'oro.address.region.entity_label',
                    'class'         => 'OroAddressBundle:Region',
                    'random_id'     => true,
                    'choice_label'  => 'name',
                    'query_builder' => null,
                    'choices'       => $choices,
                    'country'       => null,
                    'country_field' => null,
                    'configs' => array(
                        'placeholder' => 'oro.address.form.choose_region',
                        'allowClear' => true
                    ),
                    'placeholder' => '',
                    'empty_data'  => null
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['country_field'] = $form->getConfig()->getAttribute(self::COUNTRY_OPTION_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Select2TranslatableEntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oro_region';
    }
}
