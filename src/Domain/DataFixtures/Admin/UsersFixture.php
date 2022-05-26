<?php

namespace BeyondCapable\Domain\DataFixtures\Admin;

use BeyondCapable\Domain\Entity\Admin\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UsersFixture
 *
 * @package App\DataFixtures\Admin
 */
class UsersFixture extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $luyanda = new User();

        $luyanda->setFirstName('Luyanda');
        $luyanda->setLastName('Siko');
        $luyanda->setUsername('maxwell');
        $luyanda->setEmail('sikoluyanda@gmail.com');
        $luyanda->setPassword('myp@$$30r9');
        $luyanda->setActive(true);

        $manager->persist($luyanda);

        $sarah = new User();

        $sarah->setFirstName('Sarah');
        $sarah->setLastName('McLachlan');
        $sarah->setUsername('sarah.m');
        $sarah->setEmail('mclachlan.s@mclachlanmusicademy.com');
        $sarah->setPassword('$@r@hp@$$30r9');
        $sarah->setActive(false);
        $sarah->setProfileImageUrl('/public/files/profile/9807654321.png');

        $manager->persist($sarah);

        $manager->flush();
    }
}
