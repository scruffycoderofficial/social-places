<?php

namespace Oro\Bundle\AttachmentBundle\Tests\Unit\Provider;

use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\AttachmentBundle\Provider\ResizedImagePathProviderDecorator;
use Oro\Bundle\AttachmentBundle\Provider\ResizedImagePathProviderInterface;

class ResizedImagePathProviderDecoratorTest extends \PHPUnit\Framework\TestCase
{
    /** @var ResizedImagePathProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $innerResizedImagePathProvider;

    protected function setUp(): void
    {
        $this->innerResizedImagePathProvider = $this->createMock(ResizedImagePathProviderInterface::class);
    }

    /**
     * @dataProvider pathDataProvider
     */
    public function testGetPathForResizedImage(string $path, string $prefix, string $expectedPath): void
    {
        $entity = new File();
        $width = 10;
        $height = 20;

        $this->innerResizedImagePathProvider->expects(self::once())
            ->method('getPathForResizedImage')
            ->with(self::identicalTo($entity), $width, $height)
            ->willReturn($path);

        $provider = new ResizedImagePathProviderDecorator($this->innerResizedImagePathProvider, $prefix);

        self::assertEquals(
            $expectedPath,
            $provider->getPathForResizedImage($entity, $width, $height)
        );
    }

    /**
     * @dataProvider pathDataProvider
     */
    public function testGetPathForFilteredImage(string $path, string $prefix, string $expectedPath): void
    {
        $entity = new File();
        $filter = 'sample-filter';

        $this->innerResizedImagePathProvider->expects(self::once())
            ->method('getPathForFilteredImage')
            ->with(self::identicalTo($entity), $filter)
            ->willReturn($path);

        $provider = new ResizedImagePathProviderDecorator($this->innerResizedImagePathProvider, $prefix);

        self::assertEquals(
            $expectedPath,
            $provider->getPathForFilteredImage($entity, $filter)
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function pathDataProvider(): array
    {
        return [
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => 'sample/baz',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '/sample/baz/',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => 'sample/baz/',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '/sample/baz',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => 'sample/foo/bar/file.jpg',
                'prefix'       => '',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '/',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => 'sample/foo/bar/file.jpg',
                'prefix'       => '/',
                'expectedPath' => '/sample/foo/bar/file.jpg'
            ],
            [
                'path'         => 'sample/foo/bar/file.jpg',
                'prefix'       => 'sample',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => 'sample/foo/bar/file.jpg',
                'prefix'       => '/sample/',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => 'sample/foo/bar/file.jpg',
                'prefix'       => 'sample/',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => 'sample/foo/bar/file.jpg',
                'prefix'       => '/sample',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => 'sample',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '/sample/',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => 'sample/',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '/sample',
                'expectedPath' => '/foo/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => 'sample/foo',
                'expectedPath' => '/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '/sample/foo/',
                'expectedPath' => '/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => 'sample/foo/',
                'expectedPath' => '/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo/bar/file.jpg',
                'prefix'       => '/sample/foo',
                'expectedPath' => '/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo-baz/bar/file.jpg',
                'prefix'       => 'sample/foo',
                'expectedPath' => '/sample/foo-baz/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo-baz/bar/file.jpg',
                'prefix'       => '/sample/foo/',
                'expectedPath' => '/sample/foo-baz/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo-baz/bar/file.jpg',
                'prefix'       => 'sample/foo/',
                'expectedPath' => '/sample/foo-baz/bar/file.jpg'
            ],
            [
                'path'         => '/sample/foo-baz/bar/file.jpg',
                'prefix'       => '/sample/foo',
                'expectedPath' => '/sample/foo-baz/bar/file.jpg'
            ]
        ];
    }
}
