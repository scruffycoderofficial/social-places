<?php

namespace Oro\Bundle\ImportExportBundle\Manager;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\ImportExportBundle\Entity\ImportExportResult;
use Oro\Bundle\ImportExportBundle\Entity\Repository\ImportExportResultRepository;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * Responsible for saving import/export results
 */
class ImportExportResultManager
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var TokenAccessorInterface
     */
    private $tokenAccessor;

    /**
     * @param ManagerRegistry $manager
     * @param TokenAccessorInterface $tokenAccessor
     */
    public function __construct(ManagerRegistry $manager, TokenAccessorInterface $tokenAccessor)
    {
        $this->registry = $manager;
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * @param int $jobId
     * @param string $type
     * @param string $entity
     * @param User|null $owner
     * @param string|null $fileName
     * @param array $options
     *
     * @return ImportExportResult
     */
    public function saveResult(
        int $jobId,
        string $type,
        string $entity,
        User $owner = null,
        string $fileName = null,
        array $options = []
    ): ImportExportResult {
        $importExportResult = new ImportExportResult();
        $importExportResult
            ->setJobId($jobId)
            ->setEntity($entity)
            ->setFilename($fileName)
            ->setType($type)
            ->setOptions($options);

        $organization = $this->tokenAccessor->getOrganization();
        if ($organization) {
            $importExportResult->setOrganization($organization);
        }

        if ($owner) {
            $importExportResult->setOwner($owner);
        }

        /** @var EntityManager $em */
        $em = $this->registry->getManagerForClass(ImportExportResult::class);
        $em->persist($importExportResult);
        $em->flush();

        return $importExportResult;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function markResultsAsExpired(\DateTime $from, \DateTime $to): void
    {
        $em = $this->registry->getManagerForClass(ImportExportResult::class);
        /** @var ImportExportResultRepository $importExportResultRepository */
        $importExportResultRepository = $em->getRepository(ImportExportResult::class);
        $importExportResultRepository->updateExpiredRecords($from, $to);

        $em->flush();
    }
}
