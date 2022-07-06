<?php

namespace Oro\Bundle\EntityConfigBundle\Tests\Functional\Provider;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parser;
use Oro\Bundle\BatchBundle\ORM\Query\AbstractBufferedQueryResultIterator;
use Oro\Bundle\ImportExportBundle\Reader\EntityReader;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;

class ExportQueryTupleLengthProviderTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testGetTupleLength(): void
    {
        $this->assertEquals(
            $this->getTupleLengthFromQuery($this->getUserExportQuery()),
            $this->getContainer()->get('oro_entity_config.tests.provider.export_query_tuple_length')
                ->getTupleLength(User::class)
        );
    }

    /**
     * @return Query
     */
    private function getUserExportQuery(): Query
    {
        /** @var EntityReader $entityReader */
        $entityReader = $this->getContainer()->get('oro_importexport.tests.reader.entity');
        $entityReader->setSourceEntityName(User::class);
        /** @var AbstractBufferedQueryResultIterator $sourceIterator */
        $sourceIterator = $entityReader->getSourceIterator();

        return $sourceIterator->getSource();
    }

    /**
     * @param Query $query
     *
     * @return int
     */
    private function getTupleLengthFromQuery(Query $query): int
    {
        $parser = new Parser($query);
        $resultSetMapping = $parser->parse()->getResultSetMapping();

        return count($resultSetMapping->fieldMappings)
            + count($resultSetMapping->metaMappings)
            + count($resultSetMapping->scalarMappings);
    }
}
