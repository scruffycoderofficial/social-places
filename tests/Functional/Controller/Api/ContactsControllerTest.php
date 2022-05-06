<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\ContactsFixture;
use App\Entity\Contact;
use App\Tests\Concern\InteractsWithDatabase;
use App\Tests\FixtureAwareTestCase;

class ContactsControllerTest extends FixtureAwareTestCase
{
    use InteractsWithDatabase;

    private $entityClasses;

    private $entityManager;

    private $client;

    /**
     * {@inheritDoc}
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function setUp(): void
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $this->client = self::$kernel->getContainer()->get('test.client');

        $this->schemaTool = $this->getSchemaTool($this->entityManager);

        $this->entityClasses = $this->getClassMetadataCollection($this->entityManager, [
            Contact::class,
        ]);

        $this->createTables($this->schemaTool, $this->entityClasses);

        $this->addFixture(new ContactsFixture());
        $this->executeFixtures();
    }

    public function testGetContacts()
    {
        $this->client->request('GET', '/api/contacts');

        $response = $this->client->getResponse();

        $this->assertCount(2, json_decode($response->getContent()));
    }

    public function testCreateContact()
    {
        $this->client->request('POST', '/api/contact', [
            'name' => 'Luyanda Siko',
            'email' => 'sikoluyanda@gmail.com',
            'gender' => 'male',
            'content' => 'This contact should be persisted',
        ]);

        $response = $this->client->getResponse();

        $this->assertTrue(json_decode($response->getContent(), 1)['success']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropTables($this->schemaTool, $this->entityClasses);
    }
}