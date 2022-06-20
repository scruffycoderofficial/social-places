<?php

declare(strict_types=1);

namespace BeyondCapable\Tests
{
    use BeyondCapable\Core\Platform\TestWork\FixtureAwareTestCase;
    use BeyondCapable\Core\Platform\TestWork\Concern\InteractsWithDatabase;

    use Doctrine\ORM\EntityManager;

    use Doctrine\ORM\Tools\SchemaTool;
    use Doctrine\ORM\Tools\ToolsException;

    /**
     * Class TestCase
     *
     * @package BeyondCapable\Tests
     */
    abstract class TestCase extends FixtureAwareTestCase
    {
        use InteractsWithDatabase;

        /**
         * @var EntityManager
         */
        protected $entityManager;

        /**
         * @var SchemaTool
         */
        protected $schemaTool;

        /**
         * @var array
         */
        private $entityClasses = [];

        /**
         * @throws ToolsException
         */
        protected function setUp(): void
        {
            parent::setUp();

            $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

            $this->schemaTool = $this->getSchemaTool($this->entityManager);

            $this->entityClasses = $this->getClassMetadataCollection($this->entityManager, $this->getClassEntities());

            $this->createTables($this->schemaTool, $this->entityClasses);
        }

        /**
         * {@inheritDoc}
         */
        protected function tearDown(): void
        {
            parent::tearDown();

            $this->dropTables($this->schemaTool, $this->entityClasses);
        }

        private function getClassEntities(): array
        {
            return [];
        }
    }
}
