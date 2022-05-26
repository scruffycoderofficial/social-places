<?php

namespace BeyondCapable\Tests\Feature\Domain\Entity\Repository\Admin;

use BeyondCapable\Tests\TestCase;
use BeyondCapable\Domain\Entity\Admin\Contact;
use BeyondCapable\Domain\DataFixtures\Admin\ContactsFixture;

/**
 * Class ContactRepositoryTest
 *
 * @package App\Tests\Feature\Repository\Admin
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
