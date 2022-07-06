<?php
declare(strict_types=1);

namespace Oro\Component\Layout\Tests\Unit\Loader\Generator;

use Oro\Component\Layout\Loader\Generator\VisitContext;
use Oro\Component\PhpUtils\ClassGenerator;

class VisitContextTest extends \PHPUnit\Framework\TestCase
{
    public function testGetClass()
    {
        $class = new ClassGenerator(\uniqid('testClassName', false));
        $visitContext = new VisitContext($class);
        static::assertSame($class, $visitContext->getClass());
    }

    public function testAppendToUpdateMethodBody()
    {
        $visitContext = new VisitContext(new ClassGenerator());
        $visitContext->setUpdateMethodBody('echo "123";');
        $visitContext->appendToUpdateMethodBody('echo "456";');
        static::assertSame(
            <<<'CODE'
echo "123";
echo "456";
CODE
            ,
            $visitContext->getUpdateMethodBody()
        );
    }
}
