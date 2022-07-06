<?php

namespace Oro\Bundle\ApiBundle\Request;

use Oro\Bundle\ApiBundle\DataTransformer\DataTransformerRegistry;
use Oro\Bundle\ApiBundle\Processor\ApiContext;
use Oro\Bundle\ApiBundle\Util\ConfigUtil;
use Oro\Component\EntitySerializer\DataTransformerInterface;

/**
 * Provides a way to convert a value to concrete data-type for API response.
 */
class ValueTransformer
{
    /** @var DataTransformerRegistry */
    private $dataTransformerRegistry;

    /** @var DataTransformerInterface */
    private $dataTransformer;

    /**
     * @param DataTransformerRegistry  $dataTransformerRegistry
     * @param DataTransformerInterface $dataTransformer
     */
    public function __construct(
        DataTransformerRegistry $dataTransformerRegistry,
        DataTransformerInterface $dataTransformer
    ) {
        $this->dataTransformerRegistry = $dataTransformerRegistry;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * Converts a value to the given data-type using data transformers registered in the data transformer registry.
     *
     * @see \Oro\Bundle\ApiBundle\Processor\ApiContext::getNormalizationContext for the transformation context.
     *
     * @param mixed  $value    A value to be transformed.
     * @param string $dataType The data-type.
     * @param array  $context  The transformation context.
     *
     * @return mixed
     */
    public function transformValue($value, string $dataType, array $context)
    {
        if (!isset($context[ApiContext::REQUEST_TYPE])) {
            throw new \InvalidArgumentException(\sprintf(
                'The transformation context must have "%s" attribute.',
                ApiContext::REQUEST_TYPE
            ));
        }

        $dataTransformer = $this->dataTransformerRegistry->getDataTransformer(
            $dataType,
            $context[ApiContext::REQUEST_TYPE]
        );
        if (null === $dataTransformer) {
            return $value;
        }

        return $this->dataTransformer->transform(
            $value,
            [ConfigUtil::DATA_TYPE => $dataType, ConfigUtil::DATA_TRANSFORMER => [$dataTransformer]],
            $context
        );
    }

    /**
     * Converts a value of the given field using data transformer(s) from "data_transformer" configuration attribute.
     *
     * @see \Oro\Bundle\ApiBundle\Processor\ApiContext::getNormalizationContext for the transformation context.
     * @see \Oro\Bundle\ApiBundle\Config\EntityDefinitionFieldConfig::toArray() for the field configuration. Usually
     * the $excludeTargetEntity parameter is TRUE.
     *
     * @param mixed $fieldValue  A value to be transformed.
     * @param array $fieldConfig The field configuration.
     * @param array $context     The transformation context.
     *
     * @return mixed
     */
    public function transformFieldValue($fieldValue, array $fieldConfig, array $context)
    {
        return $this->dataTransformer->transform($fieldValue, $fieldConfig, $context);
    }
}
