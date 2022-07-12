<?php

namespace Oro\Bundle\AttachmentBundle\Layout\DataProvider;

use Oro\Bundle\ActionBundle\Provider\CurrentApplicationProviderInterface;
use Oro\Bundle\AttachmentBundle\Provider\FileApplicationsProvider;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;

/**
 * Layout data provider for checking if field is allowed to display on the current application.
 */
class FileApplicationsDataProvider
{
    /** @var FileApplicationsProvider */
    private $fileApplicationsProvider;

    /** @var CurrentApplicationProviderInterface */
    private $currentApplicationProvider;

    /** @var ConfigProvider */
    private $configProvider;

    /**
     * @param FileApplicationsProvider $fileApplicationsProvider
     * @param CurrentApplicationProviderInterface $currentApplicationProvider
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        FileApplicationsProvider $fileApplicationsProvider,
        CurrentApplicationProviderInterface $currentApplicationProvider,
        ConfigProvider $configProvider
    ) {
        $this->fileApplicationsProvider = $fileApplicationsProvider;
        $this->currentApplicationProvider = $currentApplicationProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @param string $className
     * @param string $fieldName
     *
     * @return bool
     */
    public function isValidForField(string $className, string $fieldName): bool
    {
        $attachmentConfig = $this->configProvider->getConfig($className, $fieldName);

        return !$attachmentConfig->is('acl_protected') ||
            $this->currentApplicationProvider->isApplicationsValid(
                $this->fileApplicationsProvider->getFileApplicationsForField($className, $fieldName)
            );
    }
}
