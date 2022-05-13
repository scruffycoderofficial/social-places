<?php

namespace App\Tests\Feature\Repository;

use App\Entity\Contact;
use App\Tests\TestCase;
use App\DataFixtures\ContactsFixture;

/**
 * Class ContactRepositoryTest
 *
 * @package App\Tests\Unit\Repository
 */
class ContactRepositoryTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

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

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}