<?php

namespace Oro\Bundle\ImapBundle\Tests\Unit\Mail\Storage;

use Oro\Bundle\ImapBundle\Mail\Storage\Body;
use Oro\Bundle\ImapBundle\Mail\Storage\Content;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BodyTest extends TestCase
{
    /** @var MockObject */
    private $part;

    /** @var Body */
    private $body;

    protected function setUp(): void
    {
        $this->part = $this->getMockBuilder('Laminas\Mail\Storage\Part')
            ->disableOriginalConstructor()
            ->getMock();

        $this->body = new Body($this->part);
    }

    public function testGetHeaders()
    {
        $headers = $this->getMockBuilder('Laminas\Mail\Headers')
            ->disableOriginalConstructor()->getMock();
        $headers->expects($this->any())->method('has')
            ->will(
                $this->returnValueMap(
                    [
                        ['Content-Type', true],
                        ['Content-Transfer-Encoding', true]
                    ]
                )
            );

        $this->part
            ->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        $result = $this->body->getHeaders();

        $this->assertTrue($headers === $result);
    }

    public function testGetHeader()
    {
        $header = $this->getMockBuilder('Laminas\Mail\Headers')
            ->disableOriginalConstructor()->getMock();
        $header->expects($this->any())->method('has')
            ->will(
                $this->returnValueMap(
                    [
                        ['Content-Type', true],
                        ['Content-Transfer-Encoding', true]
                    ]
                )
            );

        $this->part
            ->expects($this->once())
            ->method('getHeader')
            ->with($this->equalTo('SomeHeader'), $this->equalTo('string'))
            ->will($this->returnValue($header));

        $result = $this->body->getHeader('SomeHeader', 'string');

        $this->assertTrue($header === $result);
    }

    public function testGetContentSinglePartText()
    {
        $contentValue            = 'testContent';
        $contentType             = 'type/testContentType';
        $contentTransferEncoding = 'testContentTransferEncoding';
        $contentEncoding         = 'testEncoding';

        $this->part->expects($this->once())->method('isMultipart')
            ->will($this->returnValue(false));

        $this->preparePartMock($this->part, $contentValue, $contentType, $contentTransferEncoding, $contentEncoding);
        $result   = $this->body->getContent(Body::FORMAT_TEXT);
        $expected = new Content($contentValue, $contentType, $contentTransferEncoding, $contentEncoding);

        $this->assertEquals($expected, $result);
    }

    public function testGetContentSinglePartHtml()
    {
        $contentValue            = '<p>testContent</p>';
        $contentType             = 'type/testContentType';
        $contentTransferEncoding = 'testContentTransferEncoding';
        $contentEncoding         = 'testEncoding';

        $this->part->expects($this->once())->method('isMultipart')
            ->will($this->returnValue(false));

        $this->preparePartMock($this->part, $contentValue, $contentType, $contentTransferEncoding, $contentEncoding);
        $result   = $this->body->getContent(Body::FORMAT_HTML);
        $expected = new Content($contentValue, $contentType, $contentTransferEncoding, $contentEncoding);

        $this->assertEquals($expected, $result);
    }

    public function testGetContentMultipartText()
    {
        $this->part->expects($this->any())->method('isMultipart')
            ->will($this->returnValue(true));

        $part1 = $this->getMockBuilder('Laminas\Mail\Storage\Part')
            ->disableOriginalConstructor()
            ->getMock();
        $part2 = $this->getMockBuilder('Laminas\Mail\Storage\Part')
            ->disableOriginalConstructor()
            ->getMock();

        $part1->expects($this->any())->method('isMultipart')
            ->will($this->returnValue(false));
        $part2->expects($this->any())->method('isMultipart')
            ->will($this->returnValue(false));

        $this->mockIterator($this->part, $part1, $part2);
        $this->preparePartMock($part1, 'part1Content', 'text/plain', '8Bit', 'ISO-8859-1');
        $this->preparePartMock($part2, 'part2Content', 'text/html', 'Base64', 'ISO-8859-1');

        // Test to TEXT body
        $result = $this->body->getContent(Body::FORMAT_TEXT);
        $this->assertEquals(
            new Content('part1Content', 'text/plain', '8Bit', 'ISO-8859-1'),
            $result
        );

        // Test to HTML body
        $result = $this->body->getContent(Body::FORMAT_HTML);
        $this->assertEquals(
            new Content('part2Content', 'text/html', 'Base64', 'ISO-8859-1'),
            $result
        );
    }

    /**
     * @param MockObject                               $obj
     * @param                                          $iterationResult1
     * @param                                          $iterationResult2
     */
    private function mockIterator(MockObject $obj, $iterationResult1, $iterationResult2)
    {
        $obj->expects($this->exactly(3))
            ->method('current')
            ->will(
                $this->onConsecutiveCalls($iterationResult1, $iterationResult1, $iterationResult2)
            );
        $obj->expects($this->any())->method('next');
        $obj->expects($this->any())->method('rewind');
        $obj->expects($this->exactly(3))->method('valid')
            ->will($this->onConsecutiveCalls(true, true, true));
    }

    /**
     * @param MockObject $obj
     * @param            $contentValue
     * @param            $contentType
     * @param            $contentTransferEncoding
     * @param            $contentEncoding
     */
    private function preparePartMock(
        MockObject $obj,
        $contentValue,
        $contentType,
        $contentTransferEncoding,
        $contentEncoding
    ) {
        $headers = $this->getMockBuilder('Laminas\Mail\Headers')
            ->disableOriginalConstructor()->getMock();

        $headers->expects($this->any())->method('has')
            ->will(
                $this->returnValueMap(
                    [
                        ['Content-Type', true],
                        ['Content-Transfer-Encoding', true]
                    ]
                )
            );

        $obj->expects($this->any())->method('getHeaders')
            ->will($this->returnValue($headers));
        $obj->expects($this->once())->method('getContent')
            ->will($this->returnValue($contentValue));

        $contentTypeHeader = $this->createMock('Laminas\Mail\Header\ContentType');
        $contentTypeHeader->expects($this->any())->method('getType')
            ->will($this->returnValue($contentType));
        $contentTypeHeader->expects($this->any())->method('getParameter')
            ->will($this->returnValue($contentEncoding));

        $contentEncodingHeader = $this->createMock('Laminas\Mail\Header\HeaderInterface');
        $contentEncodingHeader->expects($this->any())->method('getFieldValue')
            ->will($this->returnValue($contentTransferEncoding));

        $obj->expects($this->any())->method('getHeader')
            ->will(
                $this->returnValueMap(
                    [
                        ['Content-Type', null, $contentTypeHeader],
                        ['Content-Transfer-Encoding', null, $contentEncodingHeader]
                    ]
                )
            );
    }
}
