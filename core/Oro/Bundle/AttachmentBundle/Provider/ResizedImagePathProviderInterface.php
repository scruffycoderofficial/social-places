<?php

namespace Oro\Bundle\AttachmentBundle\Provider;

use Oro\Bundle\AttachmentBundle\Entity\File;

/**
 * An interface for classes which can provide a path by which a resized/filtered image is stored for a specific file.
 */
interface ResizedImagePathProviderInterface
{
    /**
     * Gets a path to a resized image for the given file.
     *
     * @param File $entity
     * @param int  $width
     * @param int  $height
     *
     * @return string
     */
    public function getPathForResizedImage(File $entity, int $width, int $height): string;

    /**
     * Gets a path to an image with applied liip imagine filter for the given file.
     *
     * @param File   $entity
     * @param string $filterName
     *
     * @return string
     */
    public function getPathForFilteredImage(File $entity, string $filterName): string;
}
