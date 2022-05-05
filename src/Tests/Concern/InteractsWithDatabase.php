<?php

namespace App\Tests\Concern;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

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

    protected function getSchemaTool(EntityManager $entityManager): SchemaTool
    {
        return new SchemaTool($entityManager);
    }

    protected function getClassMetadataCollection(EntityManager $entityManager, array $entityClasses): array
    {
        $classes = [];

        foreach ($entityClasses as $entityClass){
            if (class_exists($entityClass)) {
                array_push($classes, $entityManager->getClassMetadata($entityClass));
            }
        }

        return $classes;
    }

    protected function createTables(SchemaTool $schemaTool, array $classes)
    {
        if (empty($classes)) {
            return;
        }

        $schemaTool->createSchema($classes);
    }

    protected function dropTables(SchemaTool $schemaTool, array $classes)
    {
        if (empty($classes)) {
            return;
        }

        $schemaTool->dropSchema($classes);
    }
}