<?php

namespace Oro\Bundle\EntityExtendBundle\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\EntityBundle\Provider\DictionaryValueListProviderInterface;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

/**
 * Implements the DictionaryValueListProviderInterface for enum entities.
 */
class EnumValueListProvider implements DictionaryValueListProviderInterface
{
    /** @var ConfigManager */
    protected $configManager;

    /** @var ManagerRegistry */
    protected $doctrine;

    /**
     * @param ConfigManager   $configManager
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ConfigManager $configManager,
        ManagerRegistry $doctrine
    ) {
        $this->configManager = $configManager;
        $this->doctrine      = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($className)
    {
        $extendConfigProvider = $this->configManager->getProvider('extend');

        return
            $extendConfigProvider->hasConfig($className)
            && ExtendHelper::isEnumValueEntityAccessible($extendConfigProvider->getConfig($className));
    }

    /**
     * {@inheritdoc}
     */
    public function getValueListQueryBuilder($className)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManagerForClass($className);
        $qb = $em->getRepository($className)->createQueryBuilder('e');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializationConfig($className)
    {
        /** @var EntityManager $em */
        $em                   = $this->doctrine->getManagerForClass($className);
        $metadata             = $em->getClassMetadata($className);
        $extendConfigProvider = $this->configManager->getProvider('extend');

        $fields = [];
        foreach ($metadata->getFieldNames() as $fieldName) {
            $extendFieldConfig = $extendConfigProvider->getConfig($className, $fieldName);
            if ($extendFieldConfig->is('is_extend')) {
                // skip extended fields
                continue;
            }

            $fields[$fieldName] = null;
        }
        $fields['order'] = ['property_path' => 'priority'];
        unset($fields['priority']);

        return [
            'exclusion_policy' => 'all',
            'hints'            => ['HINT_TRANSLATABLE'],
            'fields'           => $fields
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedEntityClasses()
    {
        $result = [];

        $extendConfigProvider = $this->configManager->getProvider('extend');
        foreach ($extendConfigProvider->getConfigs(null, true) as $extendConfig) {
            if (ExtendHelper::isEnumValueEntityAccessible($extendConfig)) {
                $result[] = $extendConfig->getId()->getClassName();
            }
        }

        return $result;
    }
}
