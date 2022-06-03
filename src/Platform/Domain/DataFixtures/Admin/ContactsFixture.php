<?php

namespace BeyondCapable\Platform\Domain\DataFixtures\Admin;

use BeyondCapable\Platform\Domain\Entity\Admin\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ContactsFixture
 *
 * @package App\DataFixtures
 */
class ContactsFixture extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $jDoe = new Contact();

        $jDoe->setName('John Doe')
            ->setEmail('jdoe@example.com')
            ->setGender('male')
            ->setContent('Testing database functionality for John Doe.');

        $manager->persist($jDoe);

        $mDoe = new Contact();

        $mDoe->setName('Marry Doe')
            ->setEmail('mdoe@example.com')
            ->setGender('female')
            ->setContent('Testing database functionality for Mary Doe.');

        $manager->persist($mDoe);

        $manager->flush();
    }
}
