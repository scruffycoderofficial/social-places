<?php

namespace BeyondCapable\Core\Platform\Domain\Tests\Functional\Entity;

use BeyondCapable\Core\Platform\Domain\Entity\Person;
use BeyondCapable\Core\Platform\DataFixtures\PersonsFixture;
use BeyondCapable\Core\Platform\Domain\Tests\Functional\FunctionalTestCase;
use BeyondCapable\Core\Platform\TestWork\Concern\InteractsWithDatabase;
use Doctrine\ORM\Tools\ToolsException;

/**
 * Class ContactRepositoryTest
 *
 * @package App\Tests\Unit\Repository
 */
class PersonTest extends FunctionalTestCase
{
    use InteractsWithDatabase;

    private $entities = [
        Person::class,
    ];

    /**
     * {@inheritDoc}
     * @throws ToolsException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Get EntityManager from the booted Kernel
        $this->entityManager = $this->getEntityManager();

        // Assign the SchemaTool and expected Entity classes
        $this->schemaTool = $this->getSchemaTool($this->entityManager);
        $this->entities = $this->loadedEntityClassMetadata($this->entities);

        // Create the expected tables
        $this->createTables($this->schemaTool, $this->entities);

        // Add expected fixtures
        $this->addFixture(new PersonsFixture());

        // Execute loaded fixtures
        $this->executeFixtures();
    }

    public function test_it_can_record_a_person()
    {
        $person = (new Person())
            ->setFirstName('Luyanda')
            ->setLastName('Siko')
            ->setEmailAddress('sikoluyanda@gmail.com')
            ->setPhoneNumber('0865431234');
        $this->entityManager->persist($person);
        $this->entityManager->flush();

        $storedPerson = $this->entityManager->getRepository(Person::class)->find(['id'=> 51]);

        $this->assertSame($person->getFirstName(), $storedPerson->getFirstName());
        $this->assertSame($person->getLastName(), $storedPerson->getLastName());
        $this->assertSame($person->getEmailAddress(), $storedPerson->getEmailAddress());
        $this->assertSame($person->getPhoneNumber(), $storedPerson->getPhoneNumber());

        $this->assertCount(51, $this->entityManager->getRepository(Person::class)->findAll());
    }

    public function test_it_can_find_a_recored_person(): void
    {
        $person = $this->entityManager->getRepository(Person::class)->find(45);

        $this->assertIsString($person->getFirstName());
        $this->assertIsString($person->getLastName());
        $this->assertIsString($person->getEmailAddress());
        $this->assertIsString($person->getPhoneNumber());
    }

    public function test_it_returns_expected_number_of_persons()
    {
        $this->assertCount(50, $this->entityManager->getRepository(Person::class)->findAll());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropTables($this->schemaTool,$this->entities);
    }
}