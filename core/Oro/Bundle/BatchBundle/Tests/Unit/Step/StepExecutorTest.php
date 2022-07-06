<?php

namespace Oro\Bundle\BatchBundle\Tests\Unit\Step;

use Oro\Bundle\BatchBundle\Step\StepExecutor;
use Oro\Bundle\BatchBundle\Tests\Unit\Step\Stub\Processor;
use Oro\Bundle\BatchBundle\Tests\Unit\Step\Stub\Reader;
use Oro\Bundle\BatchBundle\Tests\Unit\Step\Stub\WarningHandler;
use Oro\Bundle\BatchBundle\Tests\Unit\Step\Stub\Writer;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class StepExecutorTest extends WebTestCase
{
    /**
     * @throws \Exception
     */
    public function testExceptions(): void
    {
        $stepExecutor = $this->getStepExecutor([
            Reader::INVALID_ITEM,
            Processor::INVALID_ITEM,
            Writer::INVALID_ITEM,
            null
        ]);

        $warningHandler = new WarningHandler();
        $stepExecutor->execute($warningHandler);

        $this->assertExceptions($stepExecutor, $warningHandler->getWarnings());
    }

    private function assertExceptions(StepExecutor $stepExecutor, $actual): void
    {
        $expected = [
            [
                $stepExecutor->getReader(),
                Reader::class,
                Reader::INVALID_ITEM_EXCEPTION_MESSAGE,
                ['parameters' => ['option']],
                [Reader::INVALID_ITEM]
            ],
            [
                $stepExecutor->getProcessor(),
                Processor::class,
                Processor::INVALID_ITEM_EXCEPTION_MESSAGE,
                ['parameters' => ['option']],
                [Processor::INVALID_ITEM]
            ],
            [
                $stepExecutor->getWriter(),
                Writer::class,
                Writer::INVALID_ITEM_EXCEPTION_MESSAGE,
                ['parameters' => ['option']],
                [Writer::INVALID_ITEM]],
            [
                $stepExecutor->getReader(),
                Reader::class,
                Reader::LOGIC_EXCEPTION_MESSAGE,
                [],
                null
            ],
            [
                $stepExecutor->getProcessor(),
                Processor::class,
                Processor::LOGIC_EXCEPTION_MESSAGE,
                [],
                null
            ],
            [
                $stepExecutor->getWriter(),
                Writer::class,
                Writer::LOGIC_EXCEPTION_MESSAGE,
                [],
                null
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array $items
     *
     * @return StepExecutor
     */
    private function getStepExecutor(array $items = []): StepExecutor
    {
        $stepExecutor = new StepExecutor();
        $reader = new Reader($items);
        $writer = new Writer();
        $processor = new Processor();

        $stepExecutor->setBatchSize(1);
        $stepExecutor->setReader($reader);
        $stepExecutor->setWriter($writer);
        $stepExecutor->setProcessor($processor);

        return $stepExecutor;
    }
}
