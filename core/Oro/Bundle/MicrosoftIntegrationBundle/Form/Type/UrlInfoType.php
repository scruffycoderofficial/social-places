<?php

namespace Oro\Bundle\MicrosoftIntegrationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * Type for displaying URL for certain route and params
 */
class UrlInfoType extends AbstractType
{
    public const DEFAULT_DISPLAY_ROUTE = 'oro_imap_microsoft_access_token';
    public const DEFAULT_DISPLAY_ROUTE_PARAMS = [];

    /** @var RouterInterface */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $url = $this->router->generate($options['route'], $options['route_params'], $options['url_type']);
            $event->setData($url);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('route', self::DEFAULT_DISPLAY_ROUTE);
        $resolver->setDefault('route_params', self::DEFAULT_DISPLAY_ROUTE_PARAMS);
        $resolver->setDefault('url_type', RouterInterface::ABSOLUTE_URL);
    }
}
