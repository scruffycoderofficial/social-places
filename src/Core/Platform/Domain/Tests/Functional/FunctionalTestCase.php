<?php

namespace BeyondCapable\Core\Platform\Domain\Tests\Functional;

use BeyondCapable\Core\Platform\TestWork\FixtureAwareTestCase;

use Doctrine\ORM\Tools\SchemaTool;

abstract class FunctionalTestCase extends FixtureAwareTestCase
{
    protected $entityManager;

    protected function getEntityManager()
    {
        $this->entityManager = static::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        return $this->entityManager;
    }

    protected function schemaTool($entityManager = null): SchemaTool
    {
        return $this->getSchemaTool(is_null($entityManager) ? $entityManager : $this->entityManager);
    }

    protected function loadedEntityClassMetadata($entityClasses): array
    {
        return $this->getClassMetadataCollection($this->getEntityManager(), $entityClasses);
    }
}