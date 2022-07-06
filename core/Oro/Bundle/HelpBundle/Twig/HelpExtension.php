<?php

namespace Oro\Bundle\HelpBundle\Twig;

use Oro\Bundle\HelpBundle\Provider\HelpLinkProvider;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides a Twig function to retrieve the online help URL:
 *   - get_help_link
 */
class HelpExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return HelpLinkProvider
     */
    private function getHelpLinkProvider()
    {
        return $this->container->get('oro_help.help_link_provider');
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_help_link', [$this, 'getHelpLinkUrl'])
        ];
    }

    /**
     * Get help link
     *
     * @return bool
     */
    public function getHelpLinkUrl()
    {
        return $this->getHelpLinkProvider()->getHelpLinkUrl();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            'oro_help.help_link_provider' => HelpLinkProvider::class,
        ];
    }
}
