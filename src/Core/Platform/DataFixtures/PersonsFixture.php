<?php

namespace BeyondCapable\Core\Platform\DataFixtures;

use BeyondCapable\Core\Platform\Domain\Entity\Person;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PersonsFixture implements FixtureInterface
{
    private $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();

        $this->faker->addProvider(new \Faker\Provider\en_ZA\Person($this->faker));
    }

    public function load(ObjectManager $manager)
    {
        for($j=1; $j<=50; $j++){
            $manager->persist($this->createPerson(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->email,
                $this->faker->phoneNumber
            ));
        }

        $manager->flush();
    }

    public function createPerson($firstName, $lastName, $emailAddress, $phoneNumber): Person
    {
        return (new Person())
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmailAddress($emailAddress)
            ->setPhoneNumber($phoneNumber);
    }
}