<?php

namespace App\Tests\Unit\Repository;

use App\DataFixtures\ContactsFixture;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Tests\Concern\InteractsWithDatabase;
use App\Tests\FixtureAwareTestCase;

/**
 * Class ContactRepositoryTest
 *
 * @package App\Tests\Unit\Repository
 */
class ContactRepositoryTest extends FixtureAwareTestCase
{
    use InteractsWithDatabase;

    private $entityManager;

    private $entityClasses;

    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Get DoctrineORM EntityManager
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        // Initialize SchemaTool
        $this->schemaTool = $this->getSchemaTool($this->entityManager);

        /**
         * Test Case entity classes to generate
         * Table Schema from for our Database
         */
        $this->entityClasses = $this->getClassMetadataCollection($this->entityManager, [
            Contact::class,
        ]);

        // Create the tables we will be testing against
        $this->createTables($this->schemaTool, $this->entityClasses);

        // Seed tables with test data
        $this->addFixture(new ContactsFixture());
        $this->executeFixtures();
    }

    public function testFindContact(): void
    {
        $john = $this->entityManager->getRepository(Contact::class)->find(1);
        $mary = $this->entityManager->getRepository(Contact::class)->find(2);

        $this->assertEquals('John Doe', $john->getName());
        $this->assertEquals('Marry Doe', $mary->getName());
    }

    public function testContactsCount()
    {
        $this->assertCount(2, $this->entityManager->getRepository(Contact::class)->findAll());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropTables($this->schemaTool, $this->entityClasses);
    }
}