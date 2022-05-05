<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ContactsFixture
 *
 * @package App\DataFixtures
 */
class ContactsFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $jDoe = new Contact();

        $jDoe->setName('John Doe')
            ->setEmail('jdoe@example.com')
            ->setGender('male') // Create an ENUM type for Gender, for example GenderType
            ->setContent('Testing database functionality for John Doe.');

        $manager->persist($jDoe);

        $mDoe = new Contact();

        $mDoe->setName('Marry Doe')
            ->setEmail('mdoe@example.com')
            ->setGender('female') // Create an ENUM type for Gender, for example GenderType
            ->setContent('Testing database functionality for Mary Doe.');

        $manager->persist($mDoe);

        $manager->flush();
    }
}