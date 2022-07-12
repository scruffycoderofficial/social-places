<?php

namespace Oro\Bundle\SearchBundle\Exception;

/**
 * Type conversion exception.
 */
class TypeCastingException extends LogicException
{
    /**
     * @param string $fieldType
     */
    public function __construct(string $fieldType)
    {
        parent::__construct(sprintf('The value cannot be cast to the "%s" type.', $fieldType));
    }
}
