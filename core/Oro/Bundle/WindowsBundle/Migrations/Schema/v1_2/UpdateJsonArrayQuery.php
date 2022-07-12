<?php

namespace Oro\Bundle\WindowsBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Platforms\MySQL57Platform;
use Oro\Bundle\MigrationBundle\Migration\ArrayLogger;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;
use Psr\Log\LoggerInterface;

/**
 * Update field database type for oro_windows_state.data field on mysql 5.7 to use native JSON
 */
class UpdateJsonArrayQuery extends ParametrizedMigrationQuery
{
    public function getDescription()
    {
        $logger = new ArrayLogger();
        $logger->info(
            'Convert a column with "DC2Type:json_array" type to "JSON" type on MySQL >= 5.7.8 and Doctrine 2.7'
        );
        $this->doExecute($logger, true);

        return $logger->getMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(LoggerInterface $logger)
    {
        $this->doExecute($logger);
    }

    /**
     * {@inheritdoc}
     */
    public function doExecute(LoggerInterface $logger, $dryRun = false)
    {
        $platform = $this->connection->getDatabasePlatform();
        if ($platform instanceof MySQL57Platform) {
            $updateSql = "ALTER TABLE oro_windows_state CHANGE data data JSON NOT NULL COMMENT '(DC2Type:json_array)'";

            $this->logQuery($logger, $updateSql);
            if (!$dryRun) {
                $this->connection->executeStatement($updateSql);
            }
        }
    }
}
