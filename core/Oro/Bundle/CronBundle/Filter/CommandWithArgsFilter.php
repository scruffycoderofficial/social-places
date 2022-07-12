<?php

namespace Oro\Bundle\CronBundle\Filter;

use Oro\Bundle\CronBundle\ORM\CommandArgsTokenizer;
use Oro\Bundle\FilterBundle\Datasource\FilterDatasourceAdapterInterface;
use Oro\Bundle\FilterBundle\Filter\FilterUtility;
use Oro\Bundle\FilterBundle\Filter\StringFilter;
use Oro\Bundle\FilterBundle\Form\Type\Filter\TextFilterType;
use Oro\Component\DoctrineUtils\ORM\DqlUtil;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * The filter by CLI command.
 */
class CommandWithArgsFilter extends StringFilter
{
    /** @var CommandArgsTokenizer */
    protected $tokenizer;

    /** @var FilterDatasourceAdapterInterface */
    protected $ds;

    /**
     * @param FormFactoryInterface $factory
     * @param FilterUtility        $util
     * @param CommandArgsTokenizer $tokenizer
     */
    public function __construct(
        FormFactoryInterface $factory,
        FilterUtility $util,
        CommandArgsTokenizer $tokenizer
    ) {
        parent::__construct($factory, $util);
        $this->tokenizer = $tokenizer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(FilterDatasourceAdapterInterface $ds, $data)
    {
        $this->ds = $ds;
        try {
            $data = $this->parseData($data);
        } finally {
            $this->ds = null;
        }

        if (!$data) {
            return false;
        }

        $type = $data['type'];
        $values = is_array($data['value']) ? $data['value'] : [$data['value']];
        foreach ($values as $value) {
            $parameterName = $ds->generateParameterName($this->getName());
            $this->applyFilterToClause(
                $ds,
                $this->buildComparisonExpr(
                    $ds,
                    $type,
                    $this->getFieldExpr($type),
                    $parameterName
                )
            );
            if ($this->isValueRequired($type)) {
                $ds->setParameter($parameterName, $value);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseValue(array $data)
    {
        switch ($data['type']) {
            case TextFilterType::TYPE_CONTAINS:
            case TextFilterType::TYPE_NOT_CONTAINS:
                // a value for CONTAINS and NOT CONTAINS may contains several parts
                $normalizedValue = [];
                $parts = $this->tokenizer->tokenize($data['value'], $this->ds->getDatabasePlatform());
                foreach ($parts as $part) {
                    $normalizedValue[] = sprintf('%%%s%%', $part);
                }
                $data['value'] = $normalizedValue;

                return $data;
            default:
                return parent::parseValue($data);
        }
    }

    /**
     * Returns an DQL expression the filter should be applied to
     *
     * @param int $comparisonType
     *
     * @return string
     */
    protected function getFieldExpr($comparisonType)
    {
        $dataName = array_map(
            'trim',
            preg_split('/,/', $this->get(FilterUtility::DATA_NAME_KEY), -1, PREG_SPLIT_NO_EMPTY)
        );
        switch ($comparisonType) {
            case TextFilterType::TYPE_CONTAINS:
            case TextFilterType::TYPE_NOT_CONTAINS:
                // CONTAINS and NOT CONTAINS should search in all field
                return DqlUtil::buildConcatExpr($dataName);
            default:
                // other comparisons should work only for the first column
                return reset($dataName);
        }
    }
}
