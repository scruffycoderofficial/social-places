<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Contact;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use App\Tests\Concern\InteractsWithDatabase;

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

        /**
         * We expect to have two database tables here contacts and users respectively
         */
        $this->entityClasses = $this->getClassMetadataCollection($this->entityManager, [
            Contact::class,
            User::class,
        ]);

        $this->createTables($this->schemaTool, $this->entityClasses);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropTables($this->schemaTool, $this->entityClasses);
    }
}