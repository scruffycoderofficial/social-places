<?php

namespace Oro\Bundle\FilterBundle\Tests\Unit\Filter;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\FilterBundle\Datasource\Orm\OrmFilterDatasourceAdapter;
use Oro\Bundle\FilterBundle\Filter\DictionaryFilter;
use Oro\Bundle\FilterBundle\Filter\FilterUtility;
use Oro\Bundle\FilterBundle\Form\Type\Filter\ChoiceFilterType;
use Oro\Bundle\FilterBundle\Form\Type\Filter\DictionaryFilterType;
use Oro\Bundle\FilterBundle\Tests\Unit\Filter\Fixtures\TestEnumValue;
use Oro\Component\TestUtils\ORM\Mocks\EntityManagerMock;
use Oro\Component\TestUtils\ORM\OrmTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormInterface;

class DictionaryFilterTest extends OrmTestCase
{
    /** @var EntityManagerMock */
    private $em;

    /** @var FormFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $formFactory;

    /** @var DictionaryFilter */
    private $filter;

    protected function setUp(): void
    {
        $reader = new AnnotationReader();
        $metadataDriver = new AnnotationDriver(
            $reader,
            'Oro\Bundle\FilterBundle\Tests\Unit\Filter\Fixtures'
        );

        $this->em = $this->getTestEntityManager();
        $this->em->getConfiguration()->setMetadataDriverImpl($metadataDriver);
        $this->em->getConfiguration()->setEntityNamespaces([
            'Stub' => 'Oro\Bundle\FilterBundle\Tests\Unit\Filter\Fixtures'
        ]);

        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $doctrine = $this->createMock(ManagerRegistry::class);
        $doctrine->expects(self::any())
            ->method('getManagerForClass')
            ->willReturn($this->em);

        $this->filter = new DictionaryFilter($this->formFactory, new FilterUtility());
    }

    public function testInit()
    {
        $this->filter->init('test', []);

        $paramsProperty = new \ReflectionProperty($this->filter, 'params');
        $paramsProperty->setAccessible(true);
        $params = $paramsProperty->getValue($this->filter);

        self::assertEquals(
            [FilterUtility::FRONTEND_TYPE_KEY => 'dictionary', 'options' => []],
            $params
        );
    }

    public function testInitWithNullValue()
    {
        $this->filter->init('test', ['null_value' => ':empty:']);

        $paramsProperty = new \ReflectionProperty($this->filter, 'params');
        $paramsProperty->setAccessible(true);
        $params = $paramsProperty->getValue($this->filter);

        self::assertEquals(
            [FilterUtility::FRONTEND_TYPE_KEY => 'dictionary', 'null_value' => ':empty:', 'options' => []],
            $params
        );
    }

    public function testInitWithClass()
    {
        $this->filter->init('test', ['class' => 'Test\EnumValue']);

        $paramsProperty = new \ReflectionProperty($this->filter, 'params');
        $paramsProperty->setAccessible(true);
        $params = $paramsProperty->getValue($this->filter);

        self::assertEquals(
            [FilterUtility::FRONTEND_TYPE_KEY => 'dictionary', 'options' => ['class' => 'Test\EnumValue']],
            $params
        );
    }

    public function testInitWithDictionaryCode()
    {
        $this->filter->init('test', ['dictionary_code' => 'test_dictionary']);

        $paramsProperty = new \ReflectionProperty($this->filter, 'params');
        $paramsProperty->setAccessible(true);
        $params = $paramsProperty->getValue($this->filter);

        self::assertEquals(
            [FilterUtility::FRONTEND_TYPE_KEY => 'dictionary', 'options' => ['dictionary_code' => 'test_dictionary']],
            $params
        );
    }

    public function testGetForm()
    {
        $form = $this->createMock(FormInterface::class);

        $this->formFactory->expects(self::once())
            ->method('create')
            ->with(DictionaryFilterType::class)
            ->willReturn($form);

        self::assertSame($form, $this->filter->getForm());
    }

    public function testApply()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('o.id')
            ->from('Stub:TestEntity', 'o');

        $values = [
            new TestEnumValue('val1', 'Value1'),
            new TestEnumValue('val2', 'Value2')
        ];
        $data   = [
            'value' => $values
        ];

        $params = [
            'null_value'                 => ':empty:',
            FilterUtility::DATA_NAME_KEY => 'o.values'
        ];
        $this->filter->init('test', $params);

        /** @var OrmFilterDatasourceAdapter|\PHPUnit\Framework\MockObject\MockObject $ds */
        $ds = $this->getMockBuilder(OrmFilterDatasourceAdapter::class)
            ->onlyMethods(['generateParameterName'])
            ->setConstructorArgs([$qb])
            ->getMock();
        $ds->expects(self::once())
            ->method('generateParameterName')
            ->willReturn('param1');

        $this->filter->apply($ds, $data);

        $result = $qb->getQuery()->getDQL();

        self::assertEquals('SELECT o.id FROM Stub:TestEntity o WHERE test IN(:param1)', $result);
        self::assertEquals($values, $qb->getParameter('param1')->getValue());
    }

    public function testApplyNot()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('o.id')
            ->from('Stub:TestEntity', 'o');

        $values = [
            new TestEnumValue('val1', 'Value1'),
            new TestEnumValue('val2', 'Value2')
        ];
        $data   = [
            'type'  => ChoiceFilterType::TYPE_NOT_CONTAINS,
            'value' => $values
        ];

        $params = [
            'null_value'                 => ':empty:',
            FilterUtility::DATA_NAME_KEY => 'o.values'
        ];
        $this->filter->init('test', $params);

        /** @var OrmFilterDatasourceAdapter|\PHPUnit\Framework\MockObject\MockObject $ds */
        $ds = $this->getMockBuilder(OrmFilterDatasourceAdapter::class)
            ->onlyMethods(['generateParameterName'])
            ->setConstructorArgs([$qb])
            ->getMock();

        $ds->expects(self::once())
            ->method('generateParameterName')
            ->willReturn('param1');

        $this->filter->apply($ds, $data);

        $result = $qb->getQuery()->getDQL();

        self::assertEquals('SELECT o.id FROM Stub:TestEntity o WHERE test NOT IN(:param1)', $result);
        self::assertEquals($values, $qb->getParameter('param1')->getValue());
    }

    public function testPrepareData()
    {
        $data = [];
        self::assertSame($data, $this->filter->prepareData($data));
    }
}
