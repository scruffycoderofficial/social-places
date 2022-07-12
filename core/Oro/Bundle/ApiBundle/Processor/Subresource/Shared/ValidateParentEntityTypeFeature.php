<?php

namespace Oro\Bundle\ApiBundle\Processor\Subresource\Shared;

use Oro\Bundle\ApiBundle\Config\Extension\FeatureConfigurationExtension;
use Oro\Bundle\ApiBundle\Processor\Subresource\SubresourceContext;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureChecker;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Validates whether an feature is enabled for the type of entities specified
 * in the "parentClass" property of the context.
 */
class ValidateParentEntityTypeFeature implements ProcessorInterface
{
    /** @var FeatureChecker */
    private $featureChecker;

    /**
     * @param FeatureChecker $featureChecker
     */
    public function __construct(FeatureChecker $featureChecker)
    {
        $this->featureChecker = $featureChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var SubresourceContext $context */

        if (!$this->featureChecker->isResourceEnabled(
            $context->getParentClassName(),
            FeatureConfigurationExtension::API_RESOURCE_KEY
        )) {
            throw new NotFoundHttpException();
        }
    }
}
