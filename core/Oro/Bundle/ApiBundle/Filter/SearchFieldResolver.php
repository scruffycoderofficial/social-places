<?php

namespace Oro\Bundle\ApiBundle\Filter;

use Oro\Bundle\ApiBundle\Exception\InvalidFilterException;
use Oro\Bundle\SearchBundle\Query\Expression\FieldResolverInterface;
use Oro\Bundle\SearchBundle\Query\Query;

/**
 * Implements field names mapping for search filters.
 */
class SearchFieldResolver implements FieldResolverInterface
{
    /** @var array [field name in search index => ['type' => field data-type, ...], ...] */
    private $searchFieldMappings;

    /** @var array [field name => field name in search index, ...] */
    private $fieldMappings;

    /** @var array [field name pattern => field name pattern in search index, ...] */
    private $placeholderFieldMappings;

    /**
     * @param array $searchFieldMappings
     * @param array $fieldMappings
     */
    public function __construct(array $searchFieldMappings, array $fieldMappings)
    {
        $this->searchFieldMappings = $searchFieldMappings;
        $this->fieldMappings = $fieldMappings;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFieldName(string $fieldName): string
    {
        return $this->resolveFieldByFieldMappings($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFieldType(string $fieldName): string
    {
        $fieldName = $this->resolveFieldByFieldMappings($fieldName, true);
        if (isset($this->searchFieldMappings[$fieldName]['type'])
            && $this->searchFieldMappings[$fieldName]['type']
        ) {
            return $this->searchFieldMappings[$fieldName]['type'];
        }

        return Query::TYPE_TEXT;
    }

    /**
     * Gets a list of possible alternative names for the given field.
     *
     * @param string $fieldName
     *
     * @return string[]
     */
    protected function guessFieldNames(string $fieldName): array
    {
        return [];
    }

    /**
     * @param string $fieldName
     * @param bool   $replacePlaceholdersWithVariableNames
     *
     * @return string
     */
    private function resolveFieldByFieldMappings(
        string $fieldName,
        bool $replacePlaceholdersWithVariableNames = false
    ): string {
        if (isset($this->fieldMappings[$fieldName])) {
            return $this->fieldMappings[$fieldName];
        }

        $resolvedFieldName = $this->resolveFieldByPlaceholderFieldMappings(
            $fieldName,
            $replacePlaceholdersWithVariableNames
        );
        if ($resolvedFieldName) {
            return $resolvedFieldName;
        }

        $guessedFieldNames = $this->guessFieldNames($fieldName);
        foreach ($guessedFieldNames as $resolvedFieldName) {
            if (isset($this->searchFieldMappings[$resolvedFieldName])) {
                return $resolvedFieldName;
            }
        }

        if (!isset($this->searchFieldMappings[$fieldName])) {
            throw $this->createFieldNotSupportedException($fieldName);
        }

        return $fieldName;
    }

    /**
     * @param string $fieldName
     * @param bool   $replacePlaceholdersWithVariableNames
     *
     * @return string|null
     */
    private function resolveFieldByPlaceholderFieldMappings(
        string $fieldName,
        bool $replacePlaceholdersWithVariableNames
    ): ?string {
        $this->ensurePlaceholderFieldMappingsInitialized();
        foreach ($this->placeholderFieldMappings as $pattern => $searchPattern) {
            if (!\preg_match($pattern, $fieldName, $matches)) {
                continue;
            }

            $searchFieldName = $searchPattern;
            $searchMappingFieldName = $searchPattern;
            foreach ($matches as $key => $val) {
                if (\is_numeric($key)) {
                    continue;
                }
                $placeholder = \sprintf('{%s}', $key);
                $searchFieldName = \str_replace($placeholder, $val, $searchFieldName);
                $searchMappingFieldName = \str_replace($placeholder, $key, $searchMappingFieldName);
            }

            if (!isset($this->searchFieldMappings[$searchMappingFieldName])) {
                throw $this->createFieldNotSupportedException($fieldName);
            }

            return $replacePlaceholdersWithVariableNames
                ? $searchMappingFieldName
                : $searchFieldName;
        }

        return null;
    }

    private function ensurePlaceholderFieldMappingsInitialized(): void
    {
        if (null !== $this->placeholderFieldMappings) {
            return;
        }

        $this->placeholderFieldMappings = [];
        foreach ($this->fieldMappings as $fieldName => $searchFieldName) {
            if (false !== \strpos($fieldName, '(?')) {
                $this->placeholderFieldMappings[\sprintf('#%s#', $fieldName)] = $searchFieldName;
            }
        }
    }

    /**
     * @param string $fieldName
     *
     * @return InvalidFilterException
     */
    private function createFieldNotSupportedException(string $fieldName): InvalidFilterException
    {
        return new InvalidFilterException(\sprintf('The field "%s" is not supported.', $fieldName));
    }
}
