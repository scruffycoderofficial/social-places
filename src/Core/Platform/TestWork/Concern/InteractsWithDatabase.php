<?php

namespace BeyondCapable\Platform\TestWork\Concern;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

/**
 * Trait InteractsWithDatabase
 *
 * @package App\Tests\Concern
 */
trait InteractsWithDatabase
{
    /**
     * @var SchemaTool
     */
    protected $schemaTool;

    /**
     * @param EntityManager $entityManager
     * @return SchemaTool
     */
    protected function getSchemaTool(EntityManager $entityManager): SchemaTool
    {
        return new SchemaTool($entityManager);
    }

    /**
     * @param EntityManager $entityManager
     * @param array $entityClasses
     * @return array
     */
    protected function getClassMetadataCollection(EntityManager $entityManager, array $entityClasses): array
    {
        $classes = [];

        foreach ($entityClasses as $entityClass) {
            if (class_exists($entityClass)) {
                array_push($classes, $entityManager->getClassMetadata($entityClass));
            }
        }

        return $classes;
    }

    /**
     * @param SchemaTool $schemaTool
     * @param array $classes
     * @throws ToolsException
     */
    protected function createTables(SchemaTool $schemaTool, array $classes)
    {
        $schemaTool->createSchema($classes);
    }

    /**
     * @param SchemaTool $schemaTool
     * @param array $classes
     */
    protected function dropTables(SchemaTool $schemaTool, array $classes)
    {
        $schemaTool->dropSchema($classes);
    }
}
