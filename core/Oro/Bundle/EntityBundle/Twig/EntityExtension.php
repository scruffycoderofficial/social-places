<?php

namespace Oro\Bundle\EntityBundle\Twig;

use Oro\Bundle\EntityBundle\Fallback\EntityFallbackResolver;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityBundle\ORM\EntityAliasResolver;
use Oro\Bundle\EntityBundle\ORM\EntityIdAccessor;
use Oro\Bundle\EntityBundle\Provider\EntityNameResolver;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Acl\Util\ClassUtils;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Provides Twig functions to retrieve various entity parameters:
 *   - oro_class_name
 *   - oro_url_class_name
 *   - oro_class_alias
 *   - oro_action_params
 *   - oro_entity_fallback_value
 *
 * Provides a Twig filters to render entity name:
 *   - oro_format_name
 */
class EntityExtension extends AbstractExtension implements ServiceSubscriberInterface
{
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
     * @return DoctrineHelper
     */
    protected function getDoctrineHelper()
    {
        return $this->container->get(DoctrineHelper::class);
    }

    /**
     * @return EntityIdAccessor
     */
    protected function getEntityIdAccessor()
    {
        return $this->container->get(EntityIdAccessor::class);
    }

    /**
     * @return EntityRoutingHelper
     */
    protected function getEntityRoutingHelper()
    {
        return $this->container->get(EntityRoutingHelper::class);
    }

    /**
     * @return EntityNameResolver
     */
    protected function getEntityNameResolver()
    {
        return $this->container->get(EntityNameResolver::class);
    }

    /**
     * @return EntityAliasResolver
     */
    protected function getEntityAliasResolver()
    {
        return $this->container->get(EntityAliasResolver::class);
    }

    /**
     * @return EntityFallbackResolver
     */
    protected function getEntityFallbackResolver()
    {
        return $this->container->get(EntityFallbackResolver::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('oro_class_name', [$this, 'getClassName']),
            new TwigFunction('oro_url_class_name', [$this, 'getUrlClassName']),
            new TwigFunction('oro_class_alias', [$this, 'getClassAlias']),
            new TwigFunction('oro_action_params', [$this, 'getActionParams']),
            new TwigFunction('oro_entity_fallback_value', [$this, 'getFallbackValue']),
            new TwigFunction('oro_entity_fallback_type', [$this, 'getFallbackType']),
            new TwigFunction('oro_entity_reference', [$this, 'getEntityReference']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('oro_format_name', [$this, 'getEntityName'])
        ];
    }

    /**
     * Get FQCN of specified entity
     *
     * @param object $object
     * @param bool   $escape Set TRUE to escape the class name for insertion into a route,
     *                       replacing \ with _ characters
     *
     * @return string
     */
    public function getClassName($object, $escape = false)
    {
        if (!is_object($object)) {
            return null;
        }

        $className = ClassUtils::getRealClass($object);
        if (!$escape) {
            return $className;
        }

        return $this->getEntityRoutingHelper()->getUrlSafeClassName($className);
    }

    /**
     * Get URL safe class name based on passes class name
     *
     * @param string $className
     * @return string
     */
    public function getUrlClassName($className)
    {
        return $this->getEntityRoutingHelper()->getUrlSafeClassName($className);
    }

    /**
     * Get class alias of specified entity
     *
     * @param object $object
     * @param bool   $isPlural
     *
     * @return null|string
     */
    public function getClassAlias($object, $isPlural = false)
    {
        if (!is_object($object)) {
            return null;
        }

        $className = ClassUtils::getRealClass($object);

        return $isPlural
            ? $this->getEntityAliasResolver()->getPluralAlias($className)
            : $this->getEntityAliasResolver()->getAlias($className);
    }

    /**
     * @param object      $object
     * @param string|null $action
     *
     * @return array
     */
    public function getActionParams($object, $action = null)
    {
        if (!is_object($object)) {
            return [];
        }

        return $this->getEntityRoutingHelper()->getRouteParameters(
            $this->getClassName($object, true),
            $this->getEntityIdAccessor()->getIdentifier($object),
            $action
        );
    }

    /**
     * Returns a text representation of the given entity.
     *
     * @param object $object
     * @param string $locale
     *
     * @return string
     */
    public function getEntityName($object, $locale = null)
    {
        return $this->getEntityNameResolver()->getName($object, null, $locale);
    }

    /**
     * @param object $object
     * @param string $objectFieldName
     * @param int    $level
     *
     * @return mixed
     */
    public function getFallbackValue($object, $objectFieldName, $level = 1)
    {
        return $this->getEntityFallbackResolver()->getFallbackValue($object, $objectFieldName, $level);
    }

    /**
     * @param object $object
     * @param string $objectFieldName
     *
     * @return string
     */
    public function getFallbackType($object, $objectFieldName)
    {
        return $this->getEntityFallbackResolver()->getType($object, $objectFieldName);
    }

    /**
     * @param string $entityClass
     * @param mixed  $entityId
     *
     * @return object
     */
    public function getEntityReference($entityClass, $entityId)
    {
        return $this->getDoctrineHelper()->getEntityReference($entityClass, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oro_entity';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return [
            EntityIdAccessor::class,
            EntityRoutingHelper::class,
            EntityNameResolver::class,
            EntityAliasResolver::class,
            EntityFallbackResolver::class,
            DoctrineHelper::class
        ];
    }
}
