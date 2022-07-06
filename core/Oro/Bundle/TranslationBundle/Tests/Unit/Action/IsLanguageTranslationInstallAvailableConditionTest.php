<?php
declare(strict_types=1);

namespace Oro\Bundle\TranslationBundle\Tests\Unit\Action;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\TranslationBundle\Action\IsLanguageTranslationInstallAvailableCondition;
use Oro\Bundle\TranslationBundle\Download\TranslationMetricsProviderInterface;
use Oro\Bundle\TranslationBundle\Entity\Language;
use Oro\Bundle\TranslationBundle\Entity\Repository\LanguageRepository;
use Oro\Component\ConfigExpression\ContextAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

class IsLanguageTranslationInstallAvailableConditionTest extends \PHPUnit\Framework\TestCase
{
    private string $codeWithout;
    private string $codeNotInstalled;
    private string $codeInstalled;
    private Language $languageWithoutTranslations;
    private Language $languageNotInstalled;
    private Language $languageInstalled;
    private IsLanguageTranslationInstallAvailableCondition $condition1;
    private IsLanguageTranslationInstallAvailableCondition $condition2;
    private IsLanguageTranslationInstallAvailableCondition $condition3;

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function setUp(): void
    {
        $now = new \DateTime();

        $this->codeWithout = 'fr_FR';
        $this->codeNotInstalled = 'de_DE';
        $this->codeInstalled = 'es_ES';

        $this->languageWithoutTranslations = (new Language())->setCode($this->codeWithout);
        $this->languageNotInstalled = (new Language())->setCode($this->codeNotInstalled);
        $this->languageInstalled = (new Language())->setCode($this->codeInstalled)->setInstalledBuildDate($now);

        $languageRepository = $this->createMock(LanguageRepository::class);
        $languageRepository->method('findOneBy')->willReturnMap([
            [['code' => $this->codeWithout], null, $this->languageWithoutTranslations],
            [['code' => $this->codeNotInstalled], null, $this->languageNotInstalled],
            [['code' => $this->codeInstalled], null, $this->languageInstalled],
        ]);

        $doctrine = $this->createMock(ManagerRegistry::class);
        $doctrine->method('getRepository')->willReturnMap([
            [Language::class, null, $languageRepository]
        ]);

        $translationMetricsProvider = $this->createMock(TranslationMetricsProviderInterface::class);
        $translationMetricsProvider->method('getForLanguage')->willReturnMap([
            [
                $this->codeWithout,
                null
            ],
            [
                $this->codeNotInstalled,
                ['code' => $this->codeNotInstalled, 'lastBuildDate' => $now, 'translationStatus' => 90]
            ],
            [
                $this->codeInstalled,
                ['code' => $this->codeInstalled, 'lastBuildDate' => $now, 'translationStatus' => 90]
            ],
        ]);

        $this->condition1 = new IsLanguageTranslationInstallAvailableCondition($translationMetricsProvider, $doctrine);
        $this->condition2 = new IsLanguageTranslationInstallAvailableCondition($translationMetricsProvider, $doctrine);
        $this->condition3 = new IsLanguageTranslationInstallAvailableCondition($translationMetricsProvider, $doctrine);

        $this->condition1->setContextAccessor(new ContextAccessor());
        $this->condition2->setContextAccessor(new ContextAccessor());
        $this->condition3->setContextAccessor(new ContextAccessor());
    }
    public function testWithScalarValue(): void
    {
        $context = [];

        static::assertFalse($this->condition1->initialize([$this->codeWithout])->evaluate($context));
        static::assertTrue($this->condition2->initialize([$this->codeNotInstalled])->evaluate($context));
        static::assertFalse($this->condition3->initialize([$this->codeInstalled])->evaluate($context));
    }

    public function testWithEntityValue(): void
    {
        $context = [];

        static::assertFalse($this->condition1->initialize([$this->languageWithoutTranslations])->evaluate($context));
        static::assertTrue($this->condition2->initialize([$this->languageNotInstalled])->evaluate($context));
        static::assertFalse($this->condition3->initialize([$this->languageInstalled])->evaluate($context));
    }

    public function testWithPropertyPathToScalarValue(): void
    {
        $context = ['$' => [
            'language1' => $this->codeWithout,
            'language2' => $this->codeNotInstalled,
            'language3' => $this->codeInstalled,
        ]];

        $this->condition1->initialize([new PropertyPath('$.language1')]);
        $this->condition2->initialize([new PropertyPath('$.language2')]);
        $this->condition3->initialize([new PropertyPath('$.language3')]);

        static::assertFalse($this->condition1->evaluate($context));
        static::assertTrue($this->condition2->evaluate($context));
        static::assertFalse($this->condition3->evaluate($context));
    }

    public function testWithPropertyPathToEntityValue(): void
    {
        $context = ['$' => [
            'language1' => $this->languageWithoutTranslations,
            'language2' => $this->languageNotInstalled,
            'language3' => $this->languageInstalled,
        ]];

        $this->condition1->initialize([new PropertyPath('$.language1')]);
        $this->condition2->initialize([new PropertyPath('$.language2')]);
        $this->condition3->initialize([new PropertyPath('$.language3')]);

        static::assertFalse($this->condition1->evaluate($context));
        static::assertTrue($this->condition2->evaluate($context));
        static::assertFalse($this->condition3->evaluate($context));
    }
}
