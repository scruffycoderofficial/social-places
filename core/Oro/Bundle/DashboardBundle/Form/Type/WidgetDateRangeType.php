<?php

namespace Oro\Bundle\DashboardBundle\Form\Type;

use Oro\Bundle\FilterBundle\Form\Type\Filter\AbstractDateFilterType;
use Oro\Bundle\FilterBundle\Form\Type\Filter\DateRangeFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class WidgetDateRangeType extends AbstractType
{
    const NAME = 'oro_type_widget_date_range';

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return DateRangeFilterType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['datetime_range_metadata'] = [
            'name'                   => $view->vars['full_name'] . '[type]',
            'label'                  => $view->vars['label'],
            'choices'                => $view->children['type']->vars['choices'],
            'typeValues'             => $view->vars['type_values'],
            'dateParts'              => $view->vars['date_parts'],
            'valueTypes'             => $form->getConfig()->getOption('value_types'),
            'externalWidgetOptions'  => array_merge(
                $view->vars['widget_options'],
                ['dateVars' => $view->vars['date_vars']]
            ),
            'templateSelector'       => '#date-filter-template-wo-actions',
            'criteriaValueSelectors' => [
                'type'      => 'select[name=date_part], input[name$="[type]"]',
                'date_type' => 'input[name$="[type]"]',
                'date_part' => 'select[name=date_part]',
                'value'     => [
                    'start' => 'input[name="' . $view->vars['full_name'] . '[value][start]"]',
                    'end'   => 'input[name="' . $view->vars['full_name'] . '[value][end]"]'
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'tooltip'          => 'oro.filter.date.info',
                'required'         => false,
                'compile_date'     => false,
                'field_type'       => WidgetDateRangeValueType::class,
                'operator_choices' => [],
                'value_types'      => false,
                'all_time_value'   => true,
                'widget_options'   => [
                    'showTime'       => false,
                    'showTimepicker' => false
                ]
            ]
        );

        $resolver->setNormalizer(
            'operator_choices',
            function (Options $options) {
                return $this->getOperatorChoices($options);
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (isset($data['type']) && in_array($data['type'], AbstractDateFilterType::$valueTypes)) {
                    $data['value']['start'] = null;
                    $data['value']['end']   = null;
                }
                $event->setData($data);
            }
        );
    }

    /**
     * @param Options $options
     *
     * @return array
     *
     */
    protected function getOperatorChoices(Options $options)
    {
        $choices = [];
        if ($options['value_types']) {
            $choices = [
                $this->translator->trans('oro.dashboard.widget.filter.date_range.today')
                    => AbstractDateFilterType::TYPE_TODAY,
                $this->translator->trans('oro.dashboard.widget.filter.date_range.this_week')
                    => AbstractDateFilterType::TYPE_THIS_WEEK,
                $this->translator->trans('oro.dashboard.widget.filter.date_range.this_month')
                    => AbstractDateFilterType::TYPE_THIS_MONTH,
                $this->translator->trans('oro.dashboard.widget.filter.date_range.this_quarter')
                    => AbstractDateFilterType::TYPE_THIS_QUARTER,
                $this->translator->trans('oro.dashboard.widget.filter.date_range.this_year')
                    => AbstractDateFilterType::TYPE_THIS_YEAR,
            ];
            if ($options['all_time_value']) {
                $choices += [
                    $this->translator->trans('oro.dashboard.widget.filter.date_range.all_time')
                    => AbstractDateFilterType::TYPE_ALL_TIME,
                ];
            }
        }

        return
            $choices +
            [
                $this->translator->trans('oro.filter.form.label_date_type_between')
                => AbstractDateFilterType::TYPE_BETWEEN,
                $this->translator->trans('oro.filter.form.label_date_type_more_than')
                => AbstractDateFilterType::TYPE_MORE_THAN,
                $this->translator->trans('oro.filter.form.label_date_type_less_than')
                => AbstractDateFilterType::TYPE_LESS_THAN
            ];
    }
}
