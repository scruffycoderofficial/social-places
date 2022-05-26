<?php

namespace BeyondCapable\Tests\Feature\Domain\Entity\Repository\Admin\Meeting;

use BeyondCapable\Tests\TestCase;
use BeyondCapable\Domain\Entity\Admin\Meeting\Meeting;
use BeyondCapable\Domain\DataFixtures\Admin\Meeting\MeetingsFixture;

/**
 * Class MeetingRepositoryTest
 *
 * @package App\Tests\Feature\Repository\Admin
 */
class MeetingRepositoryTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->addFixture(new MeetingsFixture());
        $this->executeFixtures();
    }

    public function testFindMeeting(): void
    {
        $meeting = $this->entityManager->getRepository(Meeting::class)->find(1);

        $this->assertSame('To test whether meetings can be created.', $meeting->getPurpose());
    }

    public function testMeetingsCount(): void
    {
        $this->assertCount(1, $this->entityManager->getRepository(Meeting::class)->findAll());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
