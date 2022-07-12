<?php

namespace Oro\Bundle\DataGridBundle\Tests\Unit\Extension\Formatter\Property;

use Oro\Bundle\DataGridBundle\Datasource\ResultRecord;
use Oro\Bundle\DataGridBundle\Exception\InvalidArgumentException;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyConfiguration;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\TwigTemplateProperty;
use Oro\Bundle\UIBundle\Twig\Environment;
use Twig\Template;

class TwigTemplatePropertyTest extends \PHPUnit\Framework\TestCase
{
    private const TEMPLATE = 'sample_template.html.twig';

    /** @var Environment|\PHPUnit\Framework\MockObject\MockObject */
    private $twig;

    /** @var TwigTemplateProperty */
    private $property;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);

        $this->property = new TwigTemplateProperty($this->twig);
    }

    /**
     * @param array $params
     * @param array $expected
     *
     * @dataProvider valueDataProvider
     */
    public function testGetRawValue(array $params, array $expected): void
    {
        $this->property->init(PropertyConfiguration::create($params));

        $data = new \stdClass();
        $data->sampleName = 'sampleValue';
        $data->sampleDataName = 'sampleDataValue';

        $record = new ResultRecord($data);

        $template = $this->createMock(Template::class);

        $this->twig
            ->expects($this->once())
            ->method('loadTemplate')
            ->with(self::TEMPLATE)
            ->willReturn($template);

        $template
            ->expects($this->exactly(2))
            ->method('render')
            ->with(
                [
                    'record' => $record,
                ] + $expected
            );

        $this->property->getRawValue($record);

        // Checks local cache
        $this->property->getRawValue($record);
    }

    /**
     * @return array
     */
    public function valueDataProvider(): array
    {
        return [
            [
                [
                    TwigTemplateProperty::TEMPLATE_KEY => self::TEMPLATE,
                    TwigTemplateProperty::NAME_KEY => 'sampleName',
                ],
                [
                    'value' => 'sampleValue',
                ],
            ],
            [
                [
                    TwigTemplateProperty::TEMPLATE_KEY => self::TEMPLATE,
                    TwigTemplateProperty::DATA_NAME_KEY => 'sampleDataName',
                ],
                [
                    'value' => 'sampleDataValue',
                ],
            ],
            [
                [
                    TwigTemplateProperty::TEMPLATE_KEY => self::TEMPLATE,
                    TwigTemplateProperty::DATA_NAME_KEY => 'sampleDataName',
                    TwigTemplateProperty::CONTEXT_KEY => ['sampleContextKey' => 'sampleContextValue'],

                ],
                [
                    'value' => 'sampleDataValue',
                    'sampleContextKey' => 'sampleContextValue',
                ],
            ],
        ];
    }

    public function testInitWhenReservedKeys(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Context of template "sample_template" includes reserved key(s) - (record, value)'
        );

        $params = [
            TwigTemplateProperty::TEMPLATE_KEY => 'sample_template',
            TwigTemplateProperty::CONTEXT_KEY => [
                'record' => 'sample_record',
                'value' => 'sample_value',
            ],
        ];
        $this->property->init(PropertyConfiguration::create($params));
    }
}
