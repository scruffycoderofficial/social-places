<?php

namespace Oro\Bundle\LoggerBundle\Async\Extension;

use Oro\Component\MessageQueue\Consumption\AbstractExtension;
use Oro\Component\MessageQueue\Consumption\Context;
use Oro\Component\MessageQueue\Log\MessageProcessorClassProvider;
use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Writes info about the processor executed before consumption was interrupted to the log.
 * This info might be helpful to find time consuming processors.
 * The typical use case is
 * * Run 'php bin/console oro:logger:level info "X minutes" --env=prod' (this will cause to restart all consumers;
 *   all new instances of consumers will use new log level)
 * * If some consumers was not stopped after this command, wait till they stop and find in the log which
 *   processors work before they stopped.
 */
class InterruptionDetailConsumptionExtension extends AbstractExtension
{
    /** @var ContainerInterface */
    private $container;

    /** @var MessageProcessorClassProvider */
    private $messageProcessorClassProvider;

    /** @var string|null */
    private $lastProcessorClassName;

    /**
     * @param ContainerInterface            $container
     * @param MessageProcessorClassProvider $messageProcessorClassProvider
     */
    public function __construct(
        ContainerInterface $container,
        MessageProcessorClassProvider $messageProcessorClassProvider
    ) {
        $this->container = $container;
        $this->messageProcessorClassProvider = $messageProcessorClassProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function onPostReceived(Context $context)
    {
        // remember the current processor
        $this->lastProcessorClassName = $this->messageProcessorClassProvider->getMessageProcessorClass(
            $context->getMessageProcessor(),
            $context->getMessage()
        );
    }

    /**
     * @param Context $context
     */
    public function onIdle(Context $context)
    {
        // reset the extension state if no messages to process
        $this->lastProcessorClassName = null;
    }

    /**
     * {@inheritdoc}
     */
    public function onInterrupted(Context $context)
    {
        if ($this->lastProcessorClassName) {
            // reset caches to make sure that log level is up-to-date
            $this->resetService('oro_logger.cache');
            $this->resetService('oro_config.user');

            // write the processor executed just before interrupt of consuming to the log
            $context->getLogger()->info(
                sprintf(
                    'The last processor executed before interrupt of consuming was "%s"',
                    $this->lastProcessorClassName
                )
            );

            // reset the extension state
            $this->lastProcessorClassName = null;
        }
    }

    /**
     * @param string $name
     */
    private function resetService(string $name): void
    {
        $service = $this->container->get($name);
        if (!$service instanceof LazyLoadingInterface) {
            $msg = interface_exists(LazyLoadingInterface::class)
                ? sprintf('Declare the "%s" service as lazy.', $name)
                : 'Try running "composer require symfony/proxy-manager-bridge".';

            throw new \LogicException('Resetting a non-lazy manager service is not supported. ' . $msg);
        }

        $service->setProxyInitializer(
            \Closure::bind(
                function (&$wrappedInstance, LazyLoadingInterface $manager) use ($name) {
                    $name = $this->aliases[$name] ?? $name;

                    $wrappedInstance = isset($this->fileMap[$name])
                        ? $this->load($this->fileMap[$name])
                        : $this->{$this->methodMap[$name]}(false);

                    $manager->setProxyInitializer(null);

                    return true;
                },
                $this->container,
                Container::class
            )
        );
    }
}
