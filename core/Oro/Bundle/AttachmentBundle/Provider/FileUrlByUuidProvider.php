<?php

namespace Oro\Bundle\AttachmentBundle\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\AttachmentBundle\Exception\FileNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides file url by file UUID
 */
class FileUrlByUuidProvider
{
    /** @var FileUrlProviderInterface */
    private $fileUrlProvider;

    /** @var ManagerRegistry */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     * @param FileUrlProviderInterface $fileUrlProvider
     */
    public function __construct(
        ManagerRegistry $registry,
        FileUrlProviderInterface $fileUrlProvider
    ) {
        $this->registry = $registry;
        $this->fileUrlProvider = $fileUrlProvider;
    }

    /**
     * Get file URL.
     *
     * @param string $uuid
     * @param string $action
     * @param int $referenceType
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function getFileUrl(
        string $uuid,
        string $action = FileUrlProviderInterface::FILE_ACTION_GET,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->fileUrlProvider->getFileUrl(
            $this->findFileByUuid($uuid),
            $action,
            $referenceType
        );
    }

    /**
     * Get resized image URL.
     *
     * @param string $uuid
     * @param int $width
     * @param int $height
     * @param int $referenceType
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function getResizedImageUrl(
        string $uuid,
        int $width,
        int $height,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->fileUrlProvider->getResizedImageUrl(
            $this->findFileByUuid($uuid),
            $width,
            $height,
            $referenceType
        );
    }

    /**
     * Get URL to the image with applied liip imagine filter.
     *
     * @param string $uuid
     * @param string $filterName
     * @param int $referenceType
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function getFilteredImageUrl(
        string $uuid,
        string $filterName,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->fileUrlProvider->getFilteredImageUrl(
            $this->findFileByUuid($uuid),
            $filterName,
            $referenceType
        );
    }

    /**
     * @param string $uuid
     *
     * @return File
     *
     * @throws FileNotFoundException
     */
    private function findFileByUuid(string $uuid): File
    {
        /** @var File $file */
        $file = $this->registry->getRepository(File::class)->findOneByUuid($uuid);
        if (!$file) {
            throw new FileNotFoundException(sprintf('File with UUID "%s" not found', $uuid));
        }

        return $file;
    }
}
