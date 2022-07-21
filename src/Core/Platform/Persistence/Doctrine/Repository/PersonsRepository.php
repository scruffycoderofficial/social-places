<?php

namespace BeyondCapable\Core\Platform\Persistence\Doctrine\Repository;

use BeyondCapable\Core\Platform\Domain\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PersonsRepository
 *
 * @package BeyondCapable\Core\Platform\Persistence\Doctrine\Repository
 */
final class PersonsRepository
{
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(Person::class);
    }

    public function find($id): ?Person
    {
        return $this->entityRepository->find($id);
    }

    public function findAll()
    {
        return $this->entityRepository->findAll();
    }
}