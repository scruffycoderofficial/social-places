<?php

namespace Oro\Bundle\DataGridBundle\ImportExport;

use Oro\Bundle\DataGridBundle\Exception\RuntimeException;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyInterface;
use Oro\Bundle\ImportExportBundle\Context\ContextAwareInterface;
use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Converter\DataConverterInterface;
use Oro\Bundle\ImportExportBundle\Formatter\FormatterProvider;
use Oro\Bundle\ImportExportBundle\Formatter\TypeFormatterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Converts exported records to plain format.
 * - applies formatting;
 * - sorts columns according to their "order";
 * - excludes non-renderable columns.
 */
class DatagridDataConverter implements DataConverterInterface, ContextAwareInterface
{
    /**
     * @var DatagridColumnsFromContextProviderInterface
     */
    private $datagridColumnsFromContextProvider;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormatterProvider
     */
    protected $formatterProvider;

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @var TypeFormatterInterface[]
     */
    protected $formatters = [];

    /**
     * Contains grid columns cache for current context
     *
     * @var array
     */
    private $gridColumns = [];

    /**
     * @param DatagridColumnsFromContextProviderInterface $datagridColumnsFromContextProvider
     * @param TranslatorInterface $translator
     * @param FormatterProvider $formatterProvider
     */
    public function __construct(
        DatagridColumnsFromContextProviderInterface $datagridColumnsFromContextProvider,
        TranslatorInterface $translator,
        FormatterProvider $formatterProvider
    ) {
        $this->datagridColumnsFromContextProvider = $datagridColumnsFromContextProvider;
        $this->translator = $translator;
        $this->formatterProvider = $formatterProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToExportFormat(array $exportedRecord, $skipNullValues = true)
    {
        $columns = $this->getGridColumns();

        $result = [];
        foreach ($columns as $columnName => $column) {
            $val = array_key_exists($columnName, $exportedRecord) ? $exportedRecord[$columnName] : null;
            $val = $this->applyFrontendFormatting($val, $column);
            $columnLabel = $this->translator->trans($column['label']);

            $label = $columnLabel;
            if (array_key_exists($columnLabel, $result)) {
                $label = sprintf('%s_%s', $columnLabel, $columnName);
            }
            $result[$label] = $val;
        }

        return $result;
    }

    /**
     * Returns columns from either:
     * 1) datagrid columns stored in context;
     * 2) columns from datagrid configuration;
     * Caches grid columns in gridColumns property until the new context is set.
     *
     * @return array
     */
    protected function getGridColumns()
    {
        if (!$this->gridColumns) {
            $this->gridColumns = $this->datagridColumnsFromContextProvider->getColumnsFromContext($this->context);
        }

        return $this->gridColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToImportFormat(array $importedRecord, $skipNullValues = true)
    {
        throw new RuntimeException('The convertToImportFormat method is not implemented.');
    }

    /**
     * @param mixed $val
     * @param array $options
     *
     * @return string|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function applyFrontendFormatting($val, $options)
    {
        if (null !== $val) {
            $frontendType = isset($options['frontend_type']) ? $options['frontend_type'] : null;

            $formatter = $this->getFormatterForType($frontendType);
            if ($formatter) {
                $val = $formatter->formatType($val, $frontendType);
            } else {
                switch ($frontendType) {
                    case PropertyInterface::TYPE_SELECT:
                        if (isset($options['choices'][$val])) {
                            $val = $this->translator->trans($options['choices'][$val]);
                        }
                        break;
                    case PropertyInterface::TYPE_MULTI_SELECT:
                        if (is_array($val) && count($val)) {
                            $val = implode(',', array_map(function ($value) use ($options) {
                                return array_key_exists($value, $options['choices'])
                                    ? $options['choices'][$value]
                                    : '';
                            }, $val));
                        }
                        break;
                    case PropertyInterface::TYPE_HTML:
                        $val = $this->formatHtmlFrontendType(
                            $val,
                            isset($options['export_type']) ? $options['export_type'] : null
                        );
                        break;
                }
            }
        }

        return $val;
    }

    /**
     * Converts HTML to its string representation
     *
     * @param string $val
     * @param string $exportType
     *
     * @return string
     */
    protected function formatHtmlFrontendType($val, $exportType)
    {
        $result = trim(
            str_replace(
                "\xC2\xA0", // non-breaking space (&nbsp;)
                ' ',
                html_entity_decode(strip_tags($val))
            )
        );
        if ($exportType === 'list') {
            $result = preg_replace('/\s*\n\s*/', ';', $result);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function setImportExportContext(ContextInterface $context)
    {
        $this->context = $context;
        // Clear grid columns cache because it is not actual for new context.
        $this->gridColumns = [];
    }

    /**
     * @param string $type
     *
     * @return TypeFormatterInterface
     */
    protected function getFormatterForType($type)
    {
        $formatType = $this->context->getOption(FormatterProvider::FORMAT_TYPE);
        if (isset($this->formatters[$formatType][$type])) {
            return $this->formatters[$formatType][$type];
        }
        $formatter = $this->formatterProvider->getFormatterFor($formatType, $type);
        $this->formatters[$formatType][$type] = $formatter;

        return $formatter;
    }
}
