<?php

namespace Oro\Bundle\DataGridBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\Role;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{
    const SIMPLE_USER = 'simple_user';
    const SIMPLE_USER_2 = 'simple_user2';

    /** @var ContainerInterface */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('oro_user.manager');
        $organization = $manager->getRepository(Organization::class)->getFirst();
        $role = $manager->getRepository(Role::class)->findOneBy(['role' => User::ROLE_DEFAULT]);

        $user = $userManager->createUser();
        $user->setUsername(self::SIMPLE_USER)
            ->setPlainPassword('simple_password')
            ->setEmail('simple_user@example.com')
            ->setFirstName('First Name')
            ->setLastName('Last Name')
            ->setOrganization($organization)
            ->setOrganizations(new ArrayCollection([$organization]))
            ->setOwner($organization->getBusinessUnits()->first())
            ->addRole($role)
            ->setEnabled(true);
        $userManager->updateUser($user);
        $this->setReference($user->getUsername(), $user);

        $user = $userManager->createUser();
        $user->setUsername(self::SIMPLE_USER_2)
            ->setPlainPassword('simple_password2')
            ->setEmail('simple_user2@example.com')
            ->setFirstName('First Name')
            ->setLastName('Last Name')
            ->setOrganization($organization)
            ->setOrganizations(new ArrayCollection([$organization]))
            ->setOwner($organization->getBusinessUnits()->first())
            ->addRole($role)
            ->setEnabled(true);
        $userManager->updateUser($user);

        $this->setReference($user->getUsername(), $user);
    }
}
