<?php

namespace App\Tests\Feature\Repository\Admin;

use App\Tests\TestCase;
use App\Entity\Admin\User;
use App\DataFixtures\Admin\UsersFixture;

/**
 * Class UserRepositoryTest
 *
 * @package App\Tests\Feature\Repository\Admin
 */
class UserRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->addFixture(new UsersFixture());
        $this->executeFixtures();
    }

    public function testFindUsers(): void
    {
        $luyanda = $this->entityManager->getRepository(User::class)->find(1);
        $sarah = $this->entityManager->getRepository(User::class)->find(2);

        $this->assertTrue($luyanda->getActive());
        $this->assertFalse($sarah->getActive());

        $this->assertNull($luyanda->getProfileImageUrl());
        $this->assertNotNull($sarah->getProfileImageUrl());
    }

    public function testUsersCount(): void
    {
        $this->assertCount(2, $this->entityManager->getRepository(User::class)->findAll());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}