<?php

namespace Oro\Bundle\AttachmentBundle\Tests\Functional\Configurator;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;

trait AttachmentSettingsTrait
{
    /**
     * @param int $jpegQuality
     * @param int $pngQuality
     * @param bool $processorsAllowed
     */
    public function changeProcessorsParameters(
        int $jpegQuality = 85,
        int $pngQuality = 100,
        bool $processorsAllowed = true
    ): void {
        /** @var ConfigManager $configManager */
        $configManager = $this->getContainer()->get('oro_config.global');
        $configManager->set('oro_attachment.jpeg_quality', $jpegQuality);
        $configManager->set('oro_attachment.png_quality', $pngQuality);
        $configManager->set('oro_attachment.processors_allowed', $processorsAllowed);
        $configManager->flush();
    }
}
