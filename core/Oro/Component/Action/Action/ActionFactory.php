<?php

namespace Oro\Component\Action\Action;

use Oro\Component\ConfigExpression\ExpressionInterface;
use Psr\Container\ContainerInterface;

/**
 * Builds action instances.
 */
class ActionFactory implements ActionFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $types;

    /**
     * @param ContainerInterface $container
     * @param array $types
     */
    public function __construct(ContainerInterface $container, array $types = [])
    {
        $this->container = $container;
        $this->types = $types;
    }

    /**
     * @param string $type
     * @param array $options
     * @param ExpressionInterface $condition
     * @throws \RunTimeException
     * @return ActionInterface
     */
    public function create($type, array $options = [], ExpressionInterface $condition = null)
    {
        if (!$type) {
            throw new \RuntimeException('The action type must be defined');
        }

        $id = $this->types[$type] ?? null;
        if (!$id) {
            throw new \RuntimeException(sprintf('No attached service to action type named `%s`', $type));
        }

        $action = $this->container->get($id);

        if (!$action instanceof ActionInterface) {
            throw new \RuntimeException(sprintf('The service `%s` must implement `ActionInterface`', $id));
        }

        $action->initialize($options);

        if ($condition) {
            $action->setCondition($condition);
        }

        return $action;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeExists($name)
    {
        return isset($this->types[$name]);
    }
}
