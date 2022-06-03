<?php

namespace BeyondCapable\Tests\Feature\Domain\Entity\Repository\Blog;

use BeyondCapable\Tests\TestCase;
use BeyondCapable\Platform\Domain\DataFixtures\Blog\AuthorsFixture;

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
        $authorPost = $this->entityManager->getRepository(\BeyondCapable\Platform\Domain\Entity\Blog\PostAuthor::class)->find(1);

        $this->assertSame('George Abitbol', $authorPost->getName());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
