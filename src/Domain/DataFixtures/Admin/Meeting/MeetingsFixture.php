<?php

namespace BeyondCapable\Domain\DataFixtures\Admin\Meeting;

use BeyondCapable\Domain\Entity\Admin\Meeting\Meeting;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class MeetingsFixture
 *
 * @package App\DataFixtures\Admin
 */
class MeetingsFixture extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $meeting = new Meeting();

        $meeting->setDateTime(new \DateTime());
        $meeting->setPurpose('To test whether meetings can be created.');
        $meeting->setStatus('pending');

        $manager->persist($meeting);

        $manager->flush();
    }
}
