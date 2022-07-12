<?php

namespace Oro\Bundle\DataGridBundle\Event;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datagrid\ParameterBag;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class PreBuild
 * @package Oro\Bundle\DataGridBundle\Event
 *
 * This event dispatched at start of datagrid builder
 * Listeners could apply validation of config and provide changes of config
 */
class PreBuild extends Event implements GridConfigurationEventInterface
{
    const NAME = 'oro_datagrid.datagrid.build.pre';

    /** @var DatagridConfiguration */
    protected $config;

    /** @var ParameterBag */
    protected $parameters;

    public function __construct(DatagridConfiguration $config, ParameterBag $parameters)
    {
        $this->config     = $config;
        $this->parameters = $parameters;
    }

    /**
     * @return ParameterBag
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->config;
    }
}
