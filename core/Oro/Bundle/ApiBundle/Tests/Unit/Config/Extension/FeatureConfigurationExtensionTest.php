<?php

namespace Oro\Bundle\ApiBundle\Tests\Unit\Config\Extension;

use Oro\Bundle\ApiBundle\Config\Extension\FeatureConfigurationExtension;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class FeatureConfigurationExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testExtendConfigurationTree()
    {
        $treeBuilder = new TreeBuilder('testNode');
        $node = $treeBuilder->getRootNode();

        $extension = new FeatureConfigurationExtension();
        $extension->extendConfigurationTree($node->children());

        $processor = new Processor();
        $config = $processor->process(
            $treeBuilder->buildTree(),
            ['testNode' => ['api_resources' => ['resource1', 'resource1']]]
        );
        self::assertEquals(
            ['api_resources' => ['resource1', 'resource1']],
            $config
        );
    }
}
