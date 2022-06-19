<?php

declare(strict_types=1);

namespace BeyondCapable
{
    use Exception;

    use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

    use Symfony\Component\Config\Loader\LoaderInterface;
    use Symfony\Component\HttpKernel\Kernel as BaseKernel;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

    /**
     * Class Kernel
     *
     * @package BeyondCapable
     */
    class Kernel extends BaseKernel
    {
        use MicroKernelTrait;

        private const CONFIG_EXITS = '.{php,xml,yaml,yml}';

        /**
         * @param ContainerBuilder $container
         * @param LoaderInterface $loader
         * @throws Exception
         */
        protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
        {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', true);

            $confDir = $this->configDir();

            $loader->load($confDir . '/packages/*' . self::CONFIG_EXITS, 'glob');

            if (\is_dir($confDir . '/packages/' . $this->environment)) {
                $loader->load($confDir . '/packages/' . $this->environment . '/**/*' . self::CONFIG_EXITS, 'glob');
            }

            $loader->load($confDir . '/services' . self::CONFIG_EXITS, 'glob');
            $loader->load($confDir . '/services_' . $this->environment . self::CONFIG_EXITS, 'glob');
        }

        /**
         * @param RoutingConfigurator $routes
         */
        protected function configureRoutes(RoutingConfigurator $routes): void
        {
            $confDir = $this->configDir();

            if (\is_dir($confDir . '/routes/')) {
                $routes->import($confDir . '/routes/*' . self::CONFIG_EXITS, '/');
            }

            if (\is_dir($confDir . '/routes/' . $this->environment)) {
                $routes->import($confDir . '/routes/' . $this->environment . '/**/*' . self::CONFIG_EXITS, '/');
            }

            $routes->import($confDir . '/routes' . self::CONFIG_EXITS, '/');
        }

        private function configDir(): string
        {
            return $this->getProjectDir().'/src/**/*/Resources/config';
        }
    }
}
