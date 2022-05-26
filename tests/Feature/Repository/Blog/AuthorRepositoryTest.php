<?php

namespace App\Tests\Feature\Repository\Blog;

use App\Tests\TestCase;
use App\DataFixtures\Blog\AuthorsFixture;

class AuthorRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->addFixture(new AuthorsFixture());
        $this->executeFixtures();
    }

    public function testFindPostAuthor()
    {
        $authorPost = $this->entityManager->getRepository('App\Entity\Blog\PostAuthor')->find(1);

        $this->assertSame('George Abitbol', $authorPost->getName());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
