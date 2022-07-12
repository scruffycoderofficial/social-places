<?php

namespace Oro\Bundle\LocaleBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\EntityBundle\ORM\Repository\BatchIteratorInterface;
use Oro\Bundle\EntityBundle\ORM\Repository\BatchIteratorTrait;
use Oro\Bundle\LocaleBundle\Entity\Localization;

/**
 * Doctrine repository for Localization entity
 *
 * @method Localization|null findOneByName($name)
 */
class LocalizationRepository extends EntityRepository implements BatchIteratorInterface
{
    use BatchIteratorTrait;

    /**
     * @return array
     */
    public function getNames()
    {
        $qb = $this->createQueryBuilder('l');

        return $qb
            ->select('l.name')
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * @return array
     */
    public function findRootsWithChildren()
    {
        $localizations = $this->createQueryBuilder('l')
            ->addSelect('children')
            ->leftJoin('l.childLocalizations', 'children')
            ->getQuery()
            ->execute();

        return array_filter($localizations, function (Localization $localization) {
            return !$localization->getParentLocalization();
        });
    }

    /**
     * @return int
     */
    public function getLocalizationsCount()
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id) as localizationsCount')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $languageCode
     * @param string $formattingCode
     * @return Localization|null
     */
    public function findOneByLanguageCodeAndFormattingCode(string $languageCode, string $formattingCode): ?Localization
    {
        $qb = $this->createQueryBuilder('localization');

        return $qb->innerJoin('localization.language', 'language')
            ->where(
                $qb->expr()->eq('localization.formattingCode', ':formattingCode'),
                $qb->expr()->eq('language.code', ':languageCode')
            )
            ->setParameter('formattingCode', $formattingCode)
            ->setParameter('languageCode', $languageCode)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function findAllIndexedById(): array
    {
        return $this
            ->createQueryBuilder('localization', 'localization.id')
            ->orderBy('localization.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
