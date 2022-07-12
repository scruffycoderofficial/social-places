<?php

namespace Oro\Bundle\EmbeddedFormBundle\DependencyInjection;

use Oro\Component\DependencyInjection\ExtendedContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Container extension for OroEmbeddedFormBundle.
 */
class OroEmbeddedFormExtension extends Extension implements PrependExtensionInterface
{
    const SESSION_ID_FIELD_NAME_PARAM = 'oro_embedded_form.session_id_field_name';
    const CSRF_TOKEN_LIFETIME_PARAM   = 'oro_embedded_form.csrf_token_lifetime';

    const CSRF_TOKEN_STORAGE_SERVICE_ID       = 'oro_embedded_form.csrf_token_storage';
    const DEFAULT_CSRF_TOKEN_CACHE_SERVICE_ID = 'oro_embedded_form.csrf_token_cache';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');
        $loader->load('formatters.yml');
        $loader->load('block_types.yml');

        $container->setParameter(self::SESSION_ID_FIELD_NAME_PARAM, $config[Configuration::SESSION_ID_FIELD_NAME]);
        $container->setParameter(self::CSRF_TOKEN_LIFETIME_PARAM, $config[Configuration::CSRF_TOKEN_LIFETIME]);

        $csrfTokenCacheServiceId = $config[Configuration::CSRF_TOKEN_CACHE_SERVICE_ID]
            ?? self::DEFAULT_CSRF_TOKEN_CACHE_SERVICE_ID;

        $container
            ->getDefinition(self::CSRF_TOKEN_STORAGE_SERVICE_ID)
            ->replaceArgument(0, new Reference($csrfTokenCacheServiceId));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // X-Frame-Options header should be removed from embedded forms
        $securityConfig = $container->getExtensionConfig('nelmio_security');

        $embeddedFormPaths = [
            '/embedded-form/submit' => 'ALLOW',
            '/embedded-form/success' => 'ALLOW'
        ];

        if (isset($securityConfig[0]['clickjacking']['paths'])
            && is_array($securityConfig[0]['clickjacking']['paths'])) {
            $securityConfig[0]['clickjacking']['paths']
                = $embeddedFormPaths + $securityConfig[0]['clickjacking']['paths'];
        }

        /** @var ExtendedContainerBuilder $container */
        $container->setExtensionConfig('nelmio_security', $securityConfig);
    }
}
