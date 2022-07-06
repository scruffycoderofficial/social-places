<?php

namespace Oro\Bundle\ChartBundle\Form\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The form type for chart.
 */
class ChartType extends ConfigProviderAwareType
{
    /**
     * @var array
     */
    protected $optionsGroups = ['settings', 'data_schema'];

    /**
     * @var EventSubscriberInterface
     */
    protected $eventListener;

    /**
     * @param EventSubscriberInterface $eventListener
     */
    public function setEventListener(EventSubscriberInterface $eventListener)
    {
        $this->eventListener = $eventListener;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->eventListener);

        $chartConfigs = $this->getChartConfigs($options);

        $builder
            ->add(
                'name',
                ChoiceType::class,
                [
                    'label' => 'oro.chart.form.name.label',
                    'choices' => array_flip(array_map(
                        function (array $chartConfig) {
                            return $chartConfig['label'];
                        },
                        $chartConfigs
                    )),
                    'placeholder' => 'oro.chart.form.chart_empty_value'
                ]
            )
            ->add(
                'settings',
                ChartSettingsCollectionType::class,
                [
                    'chart_configs' => $chartConfigs
                ]
            );
    }

    /**
     * @param array $options
     * @return array
     */
    protected function getChartConfigs(array $options)
    {
        $result = [];
        $filterFunction = $options['chart_filter'] ?? null;
        $chartNames = $this->configProvider->getChartNames();
        foreach ($chartNames as $chartName) {
            $chartConfig = $this->configProvider->getChartConfig($chartName);
            if (null === $filterFunction || $filterFunction($chartConfig)) {
                $result[$chartName] = $chartConfig;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['chart_filter']);
        $resolver->setAllowedTypes('chart_filter', 'callable');
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
        return 'oro_chart';
    }
}
