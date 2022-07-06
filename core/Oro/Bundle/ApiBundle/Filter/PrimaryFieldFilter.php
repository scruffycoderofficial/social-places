<?php

namespace Oro\Bundle\ApiBundle\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;

/**
 * A filter that can be used to filter data by a value of "primary" field.
 * For example this filter can be used to filter data by a primary email,
 * which value is computed based on a collection of emails where each element of this collection
 * has a "primary" boolean property indicates whether an email is a primary one or not.
 * In this case, this filter will work only with emails marked as "primary".
 * Example of usage:
 * <code>
 *  filters:
 *      fields:
 *          primaryEmail:
 *              data_type: string
 *              property_path: emails
 *              type: primaryField
 *              options:
 *                  data_field: email
 * </code>
 * @see \Oro\Bundle\ApiBundle\Processor\CustomizeLoadedData\ComputePrimaryField
 */
class PrimaryFieldFilter extends ComparisonFilter
{
    /** @var string */
    private $dataField;

    /** @var string */
    private $primaryFlagField;

    /**
     * Gets a field that contains a data value.
     *
     * @return string|null
     */
    public function getDataField(): ?string
    {
        return $this->dataField;
    }

    /**
     * Sets a field that contains a data value.
     *
     * @param string|null $fieldName
     */
    public function setDataField(?string $fieldName): void
    {
        $this->dataField = $fieldName;
    }

    /**
     * Gets a field that contains a "primary" flag.
     *
     * @return string|null
     */
    public function getPrimaryFlagField(): ?string
    {
        return $this->primaryFlagField;
    }

    /**
     * Sets a field that contains a "primary" flag.
     *
     * @param string|null $fieldName
     */
    public function setPrimaryFlagField(?string $fieldName): void
    {
        $this->primaryFlagField = $fieldName;
    }

    /**
     * {@inheritdoc}
     */
    protected function createExpression(FilterValue $value = null): ?Expression
    {
        if (null === $value) {
            return null;
        }

        $field = $this->getField();
        if (!$field) {
            throw new \InvalidArgumentException('The Field must not be empty.');
        }
        if (!$this->dataField) {
            throw new \InvalidArgumentException('The DataField must not be empty.');
        }

        $expr = $this->buildExpression(
            $field . '.' . $this->dataField,
            $value->getPath(),
            $value->getOperator(),
            $value->getValue()
        );

        return Criteria::expr()->andX(
            $expr,
            Criteria::expr()->eq($field . '.' . ($this->primaryFlagField  ?: 'primary'), true)
        );
    }
}
