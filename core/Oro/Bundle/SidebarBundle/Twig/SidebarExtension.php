<?php

namespace Oro\Bundle\SidebarBundle\Twig;

use Oro\Bundle\FeatureToggleBundle\Checker\FeatureCheckerHolderTrait;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureToggleableInterface;
use Oro\Bundle\SidebarBundle\Configuration\WidgetDefinitionProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Asset\Packages as AssetHelper;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides a Twig function to retrieve sidebar widgets information:
 *   - oro_sidebar_get_available_widgets
 */
class SidebarExtension extends AbstractExtension implements FeatureToggleableInterface, ServiceSubscriberInterface
{
    use FeatureCheckerHolderTrait;

    const NAME = 'oro_sidebar';

    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return WidgetDefinitionProvider
     */
    protected function getWidgetDefinitionProvider()
    {
        return $this->container->get('oro_sidebar.widget_definition_provider');
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->container->get('translator');
    }

    /**
     * @return AssetHelper
     */
    protected function getAssetHelper()
    {
        return $this->container->get('assets.packages');
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('oro_sidebar_get_available_widgets', [$this, 'getWidgetDefinitions']),
        ];
    }

    /**
     * Gets available widgets for the given placement.
     *
     * @param string $placement
     * @return array
     */
    public function getWidgetDefinitions($placement)
    {
        $definitions = $this->getWidgetDefinitionProvider()
            ->getWidgetDefinitionsByPlacement($placement);
        $translator = $this->getTranslator();
        $assetHelper = $this->getAssetHelper();

        foreach ($definitions as $name => &$definition) {
            if (!$this->featureChecker->isResourceEnabled($name, 'sidebar_widgets')) {
                unset($definitions[$name]);
                continue;
            }

            $definition['title'] = $translator->trans($definition['title']);
            if (!empty($definition['icon'])) {
                $definition['icon'] = $assetHelper->getUrl($definition['icon']);
            }
            if (!empty($definition['dialogIcon'])) {
                $definition['dialogIcon'] = $assetHelper->getUrl($definition['dialogIcon']);
            }
        }

        return $definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'oro_sidebar.widget_definition_provider' => WidgetDefinitionProvider::class,
            'translator' => TranslatorInterface::class,
            'assets.packages' => AssetHelper::class,
        ];
    }
}
