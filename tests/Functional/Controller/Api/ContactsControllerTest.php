<?php

namespace App\Tests\Functional\Controller\Api;

use App\Tests\TestCase;
use App\DataFixtures\ContactsFixture;

class ContactsControllerTest extends TestCase
{
    private $client;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

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

        $data = json_decode($response->getContent(), true)[0];

        $this->assertSame('Luyanda Siko', $data['name']);
        $this->assertSame('sikoluyanda@gmail.com', $data['email']);
        $this->assertSame('male', $data['gender']);
        $this->assertSame('This contact should be persisted', $data['content']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}