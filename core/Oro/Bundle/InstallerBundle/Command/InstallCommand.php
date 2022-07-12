<?php
declare(strict_types=1);

namespace Oro\Bundle\InstallerBundle\Command;

use Composer\Question\StrictConfirmationQuestion;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\InstallerBundle\Command\Provider\InputOptionProvider;
use Oro\Bundle\InstallerBundle\CommandExecutor;
use Oro\Bundle\InstallerBundle\InstallerEvent;
use Oro\Bundle\InstallerBundle\InstallerEvents;
use Oro\Bundle\InstallerBundle\Persister\YamlPersister;
use Oro\Bundle\InstallerBundle\ScriptExecutor;
use Oro\Bundle\InstallerBundle\ScriptManager;
use Oro\Bundle\LocaleBundle\Command\UpdateLocalizationCommand;
use Oro\Bundle\LocaleBundle\DependencyInjection\OroLocaleExtension;
use Oro\Bundle\SecurityBundle\Command\LoadPermissionConfigurationCommand;
use Oro\Bundle\TranslationBundle\Command\OroTranslationUpdateCommand;
use Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Process\Process;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Application installer.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class InstallCommand extends AbstractCommand implements InstallCommandInterface
{

    /** @var string */
    protected static $defaultName = 'oro:install';

    private Process $assetsCommandProcess;
    private InputOptionProvider $inputOptionProvider;
    private YamlPersister $yamlPersister;
    private ScriptManager $scriptManager;
    private Registry $doctrine;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        YamlPersister $yamlPersister,
        ScriptManager $scriptManager,
        Registry $doctrine,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->yamlPersister = $yamlPersister;
        $this->scriptManager = $scriptManager;
        $this->doctrine = $doctrine;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }

    /** @SuppressWarnings(PHPMD.ExcessiveMethodLength) */
    protected function configure()
    {
        $this
            ->addOption('application-url', null, InputOption::VALUE_OPTIONAL, 'Application URL')
            ->addOption('organization-name', null, InputOption::VALUE_OPTIONAL, 'Organization name')
            ->addOption('user-name', null, InputOption::VALUE_OPTIONAL, 'Admin username')
            ->addOption('user-email', null, InputOption::VALUE_OPTIONAL, 'Admin user email')
            ->addOption('user-firstname', null, InputOption::VALUE_OPTIONAL, 'Admin user first name')
            ->addOption('user-lastname', null, InputOption::VALUE_OPTIONAL, 'Admin user last name')
            ->addOption('user-password', null, InputOption::VALUE_OPTIONAL, 'Admin user password')
            ->addOption('sample-data', null, InputOption::VALUE_OPTIONAL, 'Load sample data')
            ->addOption('language', null, InputOption::VALUE_OPTIONAL, 'Localization language code')
            ->addOption('formatting-code', null, InputOption::VALUE_OPTIONAL, 'Localization formatting code')
            ->addOption('skip-assets', null, InputOption::VALUE_NONE, 'Skip install/build of frontend assets')
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlink the assets instead of copying them')
            ->addOption('skip-download-translations', null, InputOption::VALUE_NONE, 'Skip downloading translations')
            ->addOption('skip-translations', null, InputOption::VALUE_NONE, 'Skip applying translations')
            ->addOption('drop-database', null, InputOption::VALUE_NONE, 'Delete all existing data')
            ->setDescription('Application installer.')
            // @codingStandardsIgnoreStart
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command is the application installer. It installs the application
with all schema and data migrations, prepares assets and application caches.

  <info>php %command.full_name%</info>

The <info>--application-url</info> option can be used to specify the URL at which
the management console (back-office) of the application will be available.
Please make sure that you web-server is configured properly (see more at
<comment>https://doc.oroinc.com/backend/setup/dev-environment/web-server-config/</comment>).

  <info>php %command.full_name% --application-url=<url></info>
  <info>php %command.full_name% --application-url='http://example.com/'</info>

It is also possible to modify the application URL after the installation:

  <info>php oro:config:update oro_ui.application_url 'http://example.com/'</info>

The <info>--organization-name</info> option can be used to specify your company name:

  <info>php %command.full_name% --organization-name=<company></info>
  <info>php %command.full_name% --organization-name="Acme Inc."</info>

The <info>--user-name</info>, <info>--user-email</info>, <info>--user-firstname</info>, <info>--user-lastname</info> and
<info>--user-password</info> options allow to configure the admin user account details:

  <info>php %command.full_name% --user-name=<username> --user-email=<email> --user-firstname=<firstname> --user-lastname=<lastname> --user-password=<password></info>

The <info>--sample-data</info> option can be used specify whether the demo sample data
should be loaded after the installation:

  <info>php %command.full_name% --sample-data=y</info>
  <info>php %command.full_name% --sample-data=n</info>

The <info>--language</info> and <info>--formatting</info> code options should be used to specify
the localization language and the localization formatting setting that are used
by the application:

  <info>php %command.full_name% --language=<language-code> --formatting-code=<formatting-code></info>
  <info>php %command.full_name% --language=en --formatting-code=en_US</info>

The <info>--skip-assets</info> option can be used to skip install and build
of the frontend assets:

  <info>php %command.full_name% --skip-assets</info>

The <info>--symlink</info> option tells the asset installer to create symlinks
instead of copying the assets (it may be useful during development):

  <info>php %command.full_name% --symlink</info>

The <info>--skip-download-translations</info> and <info>--skip-translations</info> options can be used
to skip the step of downloading translations (already downloaded translations
will be applied if present), or skip applying the translations completely:

  <info>php %command.full_name% --skip-download-translations</info>
  <info>php %command.full_name% --skip-translations</info>

The <info>--drop-database</info> option should be provided when reinstalling the application
from scratch on top of the existing database that needs to be wiped out first,
or otherwise the installation will fail:

  <info>php %command.full_name% --drop-database</info>

Please see below an example with the most commonly used options:

  <info>php %command.full_name% \
    -vvv \
    --env=prod \
    --timeout=600 \
    --language=en \
    --formatting-code=en_US \
    --organization-name='Acme Inc.' \
    --user-name=admin \
    --user-email=admin@example.com \
    --user-firstname=John \
    --user-lastname=Doe \
    --user-password='PleaseReplaceWithSomeStrongPassword' \
    --application-url='http://example.com/' \
    --sample-data=y</info>

Or, as a one-liner:

  <info>php %command.full_name% -vvv --env=prod --timeout=600 --language=en --formatting-code=en_US --organization-name='Acme Inc.' --user-name=admin --user-email=admin@example.com --user-firstname=John --user-lastname=Doe --user-password='PleaseReplaceWithSomeStrongPassword' --application-url='http://example.com/' --sample-data=y</info>

HELP
            )
            ->addUsage('--application-url=<url>')
            ->addUsage('--organization-name=<company>')
            ->addUsage('--user-name=<username> --user-email=<email> --user-firstname=<firstname> --user-lastname=<lastname> --user-password=<password>')
            ->addUsage('--sample-data=y')
            ->addUsage('--sample-data=n')
            ->addUsage('--language=en --formatting-code=en_US')
            ->addUsage('--skip-assets')
            ->addUsage('--symlink')
            ->addUsage('--skip-download-translations')
            ->addUsage('--skip-translations')
            ->addUsage('--drop-database')
            ->addUsage("-vvv --env=prod --timeout=600 --language=en --formatting-code=en_US --organization-name=<company> --user-name=<username> --user-email=<email> --user-firstname=<firstname> --user-lastname=<lastname> --user-password=<password> --application-url=<url> --sample-data=y")
            ->addUsage("-vvv --env=prod --timeout=600 --language=en --formatting-code=en_US --organization-name='Acme Inc.' --user-name=admin --user-email=admin@example.com --user-firstname=John --user-lastname=Doe --user-password='PleaseReplaceWithSomeStrongPassword' --application-url='http://example.com/' --sample-data=y")
            // @codingStandardsIgnoreEnd
        ;

        parent::configure();
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->inputOptionProvider = new InputOptionProvider($output, $input, $this->getHelperSet()->get('question'));

        $this->validateApplicationUrl($input->getOption('application-url'));
        if (false === $input->isInteractive()) {
            $this->validate($input);
        }

        if ($this->isInstalled()) {
            $this->alreadyInstalledMessageShow($input, $output);

            return 1;
        }

        $skipAssets = $input->getOption('skip-assets');
        $commandExecutor = $this->getCommandExecutor($input, $output);

        $output->writeln('<info>Installing Oro Application.</info>');
        $output->writeln('');

        $exitCode = $this->checkRequirements($commandExecutor);
        if ($exitCode > 0) {
            return $exitCode;
        }

        $event = new InstallerEvent($this, $input, $output, $commandExecutor);

        try {
            $this->prepareStep($input, $output);

            $this->eventDispatcher->dispatch($event, InstallerEvents::INSTALLER_BEFORE_DATABASE_PREPARATION);

            if (!$skipAssets) {
                $this->startBuildAssetsProcess($input);
            }

            $this->loadDataStep($commandExecutor, $output);
            $this->eventDispatcher->dispatch($event, InstallerEvents::INSTALLER_AFTER_DATABASE_PREPARATION);

            $this->finalStep($commandExecutor, $output, $input, $skipAssets);

            if (!$skipAssets) {
                $buildAssetsProcessExitCode = $this->getBuildAssetsProcessExitCode($output);
            }
            // cache clear must be done after assets build process finished,
            // otherwise, it could lead to unpredictable errors
            $this->clearCache($commandExecutor, $input);
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return $commandExecutor->getLastCommandExitCode();
        }

        $this->successfullyInstalledMessageShow($input, $output);

        return $buildAssetsProcessExitCode ?? 0;
    }

    private function alreadyInstalledMessageShow(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->error('An Oro application is already installed.');
        $io->text('To proceed with the installation:');
        $io->listing([
            'set <info>installed: false</info> in <info>config/parameters.yml</info>,',
            'remove caches in <info>var/cache</info> folder manually,',
            'drop the database manually or reinstall with the <info>--drop-database</info> option.',
        ]);
        $io->warning([
            'All data will be lost. Database backup is highly recommended!'
        ]);
    }

    private function successfullyInstalledMessageShow(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln(
            sprintf(
                '<info>Oro Application has been successfully installed in <comment>%s</comment> mode.</info>',
                $input->getOption('env')
            )
        );
        if ('prod' != $input->getOption('env')) {
            $output->writeln(
                '<info>To run application in <comment>prod</comment> mode, ' .
                'please run <comment>cache:clear</comment> command with <comment>--env=prod</comment> parameter</info>'
            );
        }
        if ('prod' == $input->getOption('env')) {
            $output->writeln(
                '<info>Please run <comment>oro:api:doc:cache:clear</comment> command to warm-up ' .
                'API documentation cache</info>'
            );
        }
        $output->writeln(
            '<info>Ensure that at least one consumer service is running. ' .
            'Use the <comment>oro:message-queue:consume</comment> ' .
            'command to launch a consumer service instance. See ' .
            '<comment>' .
            'https://doc.oroinc.com/backend/setup/dev-environment/manual-installation/crm-ce/' .
            '#configure-and-run-required-background-processes' .
            '</comment> ' .
            'for more information.</info>'
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validate(InputInterface $input)
    {
        $requiredParams = ['user-email', 'user-firstname', 'user-lastname', 'user-password'];
        $emptyParams    = [];

        foreach ($requiredParams as $param) {
            if (null === $input->getOption($param)) {
                $emptyParams[] = '--' . $param;
            }
        }

        if (!empty($emptyParams)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "The %s arguments are required in non-interactive mode",
                    implode(', ', $emptyParams)
                )
            );
        }

        $this->validateLocalizationOptions($input);
    }

    protected function checkRequirements(CommandExecutor $commandExecutor): int
    {
        $commandExecutor->runCommand(
            'oro:check-requirements',
            ['--ignore-errors' => true, '--verbose' => 2]
        );

        return $commandExecutor->getLastCommandExitCode();
    }

    /**
     * Drop schema, clear entity config and extend caches
     */
    protected function prepareStep(InputInterface $input, OutputInterface $output): self
    {
        if ($input->getOption('drop-database')) {
            $output->writeln('<info>Drop schema.</info>');
            $managers = $this->doctrine->getManagers();
            foreach ($managers as $name => $manager) {
                if ($manager instanceof EntityManager) {
                    $tool = new SchemaTool($manager);
                    $tool->dropDatabase();
                }
            }
        }

        return $this;
    }

    protected function getNotBlankValidator(string $message): callable
    {
        return function ($value) use ($message) {
            if (strlen(trim($value)) === 0) {
                throw new \Exception($message);
            }

            return $value;
        };
    }

    /**
     * Update the administrator user
     */
    protected function updateUser(CommandExecutor $commandExecutor): void
    {
        $emailValidator     = $this->getNotBlankValidator('The email must be specified');
        $firstNameValidator = $this->getNotBlankValidator('The first name must be specified');
        $lastNameValidator  = $this->getNotBlankValidator('The last name must be specified');
        $passwordValidator  = function ($value) {
            if (strlen(trim($value)) < 2) {
                throw new \Exception('The password must be at least 2 characters long');
            }

            return $value;
        };

        $options = [
            'user-name'      => [
                'label'                  => 'Username',
                'options'                => [
                    'constructorArgs' => [LoadAdminUserData::DEFAULT_ADMIN_USERNAME]
                ],
                'defaultValue'           => LoadAdminUserData::DEFAULT_ADMIN_USERNAME,
            ],
            'user-email'     => [
                'label'                  => 'Email',
                'options'                => ['settings' => ['validator' => [$emailValidator]]],
                'defaultValue'           => null,
            ],
            'user-firstname' => [
                'label'                  => 'First name',
                'options'                => ['settings' => ['validator' => [$firstNameValidator]]],
                'defaultValue'           => null,
            ],
            'user-lastname'  => [
                'label'                  => 'Last name',
                'options'                => ['settings' => ['validator' => [$lastNameValidator]]],
                'defaultValue'           => null,
            ],
            'user-password'  => [
                'label'                  => 'Password',
                'options'                => ['settings' => ['validator' => [$passwordValidator], 'hidden' => [true]]],
                'defaultValue'           => null,
            ],
        ];

        $commandExecutor->runCommand(
            'oro:user:update',
            array_merge(
                [
                    'user-name'           => LoadAdminUserData::DEFAULT_ADMIN_USERNAME,
                    '--process-isolation' => true
                ],
                $this->getCommandParametersFromOptions($options)
            )
        );
    }

    protected function updateOrganization(CommandExecutor $commandExecutor): void
    {
        /** @var ConfigManager $configManager */
        $configManager             = $this->getContainer()->get('oro_config.global');
        $defaultOrganizationName   = $configManager->get('oro_ui.organization_name');
        $organizationNameValidator = function ($value) use (&$defaultOrganizationName) {
            $len = strlen(trim($value));
            if ($len === 0 && empty($defaultOrganizationName)) {
                throw new \Exception('The organization name must not be empty');
            }
            if ($len > 15) {
                throw new \Exception('The organization name must be not more than 15 characters long');
            }
            return $value;
        };

        $options = [
            'organization-name' => [
                'label'                  => 'Organization name',
                'options'                => [
                    'constructorArgs' => [$defaultOrganizationName],
                    'settings' => ['validator' => [$organizationNameValidator]]
                ],
                'defaultValue'           => $defaultOrganizationName,
            ]
        ];

        $commandExecutor->runCommand(
            'oro:organization:update',
            array_merge(
                [
                    'organization-name' => 'default',
                    '--process-isolation' => true,
                ],
                $this->getCommandParametersFromOptions($options)
            )
        );
    }

    /**
     * Update system settings such as app url, company name and short name
     */
    protected function updateSystemSettings(): void
    {
        /** @var ConfigManager $configManager */
        $configManager = $this->getContainer()->get('oro_config.global');
        $options       = [
            'application-url' => [
                'label' => 'Application URL',
                'config_key' => 'oro_ui.application_url',
                'options' => [
                    'settings' => [
                        'validator' => [
                            function (?string $applicationUrl) {
                                if (!$applicationUrl) {
                                    throw new \InvalidArgumentException(
                                        'The value of the "application-url" parameter should not be blank.'
                                    );
                                }

                                $this->validateApplicationUrl($applicationUrl);

                                return $applicationUrl;
                            }
                        ]
                    ]
                ]
            ]
        ];

        foreach ($options as $optionName => $optionData) {
            $configKey    = $optionData['config_key'];
            $defaultValue = $configManager->get($configKey);

            $value = $this->inputOptionProvider->get(
                $optionName,
                $optionData['label'],
                $defaultValue,
                array_merge(['constructorArgs' => [$defaultValue]], $optionData['options'])
            );

            // update setting if it's not empty and not equal to default value
            if (!empty($value) && $value !== $defaultValue) {
                $configManager->set($configKey, $value);
            }
        }

        $configManager->flush();
    }

    protected function loadDataStep(CommandExecutor $commandExecutor, OutputInterface $output): self
    {
        $output->writeln('<info>Setting up database.</info>');

        $commandExecutor
            ->runCommand(
                'oro:migration:load',
                [
                    '--force'             => true,
                    '--process-isolation' => true,
                    '--timeout'           => $commandExecutor->getDefaultOption('process-timeout'),
                ]
            )
            ->runCommand(LoadPermissionConfigurationCommand::getDefaultName(), ['--process-isolation' => true])
            ->runCommand('oro:cron:definitions:load', ['--process-isolation' => true])
            ->runCommand('oro:workflow:definitions:load', ['--process-isolation' => true])
            ->runCommand('oro:process:configuration:load', ['--process-isolation' => true])
            ->runCommand('oro:migration:data:load', ['--process-isolation' => true, '--no-interaction' => true]);

        $output->writeln('');
        $output->writeln('<info>Administration setup.</info>');

        $this->updateSystemSettings();
        $this->updateOrganization($commandExecutor);
        $this->updateUser($commandExecutor);
        $this->updateLocalization($commandExecutor);

        $isDemo = $this->inputOptionProvider->get(
            'sample-data',
            'Load sample data (y/n)',
            null,
            [
                'class' => StrictConfirmationQuestion::class,
                'constructorArgs' => [false]
            ]
        );
        if ($isDemo) {
            // load demo fixtures
            $commandExecutor->runCommand(
                'oro:migration:data:load',
                ['--process-isolation'  => true, '--fixtures-type' => 'demo']
            );
        }

        $output->writeln('');

        return $this;
    }

    protected function finalStep(
        CommandExecutor $commandExecutor,
        OutputInterface $output,
        InputInterface $input,
        bool $skipAssets
    ): self {
        $output->writeln('<info>Preparing application.</info>');

        $this->processTranslations($input, $commandExecutor);

        // run installer scripts
        $this->processInstallerScripts($output, $commandExecutor);

        // set installed flag in DI container
        $this->updateInstalledFlag(date('c'));

        if (!$skipAssets) {
            /**
             * Place this launch of command after the launch of 'assetic-dump' in BAP-16333
             */
            $commandExecutor->runCommand('oro:translation:dump', ['--process-isolation' => true]);
        }

        $output->writeln('');

        return $this;
    }

    /**
     * Update installed flag in parameters.yml
     *
     * @param bool|string $installed
     */
    protected function updateInstalledFlag($installed): void
    {
        $params                        = $this->yamlPersister->parse();
        $params['system']['installed'] = $installed;
        $this->yamlPersister->dump($params);
    }

    protected function clearCache(CommandExecutor $commandExecutor, InputInterface $input): void
    {
        $cacheClearOptions = ['--process-isolation' => true];
        if ($commandExecutor->getDefaultOption('no-debug')) {
            $cacheClearOptions['--no-debug'] = true;
        }
        if ($input->getOption('env')) {
            $cacheClearOptions['--env'] = $input->getOption('env');
        }
        $commandExecutor->runCommand('cache:clear', $cacheClearOptions);
    }

    protected function processInstallerScripts(OutputInterface $output, CommandExecutor $commandExecutor): void
    {
        $scriptExecutor = new ScriptExecutor($output, $this->getContainer(), $commandExecutor);

        $scriptFiles   = $this->scriptManager->getScriptFiles();
        if (!empty($scriptFiles)) {
            foreach ($scriptFiles as $scriptFile) {
                $scriptExecutor->runScript($scriptFile);
            }
        }
    }

    protected function isInstalled(): bool
    {
        return $this->getContainer()->hasParameter('installed')
            && $this->getContainer()->getParameter('installed');
    }

    protected function processTranslations(InputInterface $input, CommandExecutor $commandExecutor): void
    {
        if (!$input->getOption('skip-translations')) {
            if (!$input->getOption('skip-download-translations')) {
                $commandExecutor
                    ->runCommand(
                        OroTranslationUpdateCommand::getDefaultName(),
                        ['--process-isolation' => true, '--all' => true]
                    );
            }
            $commandExecutor
                ->runCommand('oro:translation:load', ['--process-isolation' => true, '--rebuild-cache' => true]);
        }
    }

    protected function updateLocalization(CommandExecutor $commandExecutor): void
    {
        $formattingCode = $this->getContainer()->getParameter(OroLocaleExtension::PARAMETER_FORMATTING_CODE);
        $language = $this->getContainer()->getParameter(OroLocaleExtension::PARAMETER_LANGUAGE);

        $options = [
            'formatting-code' => [
                'label' => 'Formatting Code',
                'options' => [
                    'constructorArgs' => [$formattingCode],
                    'settings' => [
                        'validator' => [
                            function ($value) {
                                $this->validateFormattingCode($value);
                                return $value;
                            }
                        ]
                    ]
                ],
                'defaultValue' => $formattingCode
            ],
            'language' => [
                'label' => 'Language',
                'options' => [
                    'constructorArgs' => [$language],
                    'settings' => [
                        'validator' => [
                            function ($value) {
                                $this->validateLanguage($value);
                                return $value;
                            }
                        ]
                    ]
                ],
                'defaultValue' => $language
            ]
        ];

        $commandExecutor->runCommand(
            UpdateLocalizationCommand::getDefaultName(),
            array_merge(
                ['--process-isolation' => true],
                $this->getCommandParametersFromOptions($options)
            )
        );
    }

    private function validateLocalizationOptions(InputInterface $input): void
    {
        $formattingCode = $input->getOption('formatting-code');
        if ($formattingCode) {
            $this->validateFormattingCode($formattingCode);
        }

        $language = (string)$input->getOption('language');
        if ($language) {
            $this->validateLanguage($language);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateFormattingCode(string $locale): void
    {
        $locales = array_keys(Locales::getNames());
        if (!in_array($locale, $locales, true)) {
            throw new \InvalidArgumentException($this->getExceptionMessage('formatting', $locale, $locales));
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateLanguage(string $language): void
    {
        $locales = Locales::getLocales();
        if (!in_array($language, $locales, true)) {
            throw new \InvalidArgumentException($this->getExceptionMessage('language', $language, $locales));
        }
    }

    private function getExceptionMessage(string $optionName, string $localeCode, array $availableLocaleCodes):string
    {
        $exceptionMessage = sprintf('"%s" is not a valid %s code!', $localeCode, $optionName);
        $alternatives = $this->getAlternatives($localeCode, $availableLocaleCodes);
        if ($alternatives) {
            $exceptionMessage .= sprintf("\nDid you mean %s?\n", $alternatives);
        }

        return $exceptionMessage;
    }

    private function validateApplicationUrl(?string $applicationUrl): void
    {
        if (!$applicationUrl) {
            return;
        }

        $violations = $this->getContainer()
            ->get('validator')
            ->validate($applicationUrl, new Url());

        if (!$violations->count()) {
            return;
        }

        throw new \InvalidArgumentException(
            'The value of the "application-url" parameter is invalid. ' . $violations->get(0)->getMessage()
        );
    }

    private function getCommandParametersFromOptions(array $options): array
    {
        $commandParameters = [];
        foreach ($options as $optionName => $optionData) {
            $commandParameters['--' . $optionName] = $this->inputOptionProvider->get(
                $optionName,
                $optionData['label'],
                $optionData['defaultValue'],
                $optionData['options']
            );
        }

        return $commandParameters;
    }

    private function getAlternatives(string $name, array $items): string
    {
        $alternatives = [];
        foreach ($items as $item) {
            $lev = levenshtein($name, $item);
            if ($lev <= strlen($name) / 2 || false !== strpos($item, $name)) {
                $alternatives[$item] = $lev;
            }
        }
        asort($alternatives);

        return implode(', ', array_keys($alternatives));
    }

    private function startBuildAssetsProcess(InputInterface $input): void
    {
        $phpBinaryPath = CommandExecutor::getPhpExecutable();

        $command = [
            $phpBinaryPath,
            'bin/console',
            'oro:assets:install'
        ];

        if ($input->hasOption('symlink') && $input->getOption('symlink')) {
            $command[] = '--symlink';
        }

        if ($input->getOption('env')) {
            $command[] = sprintf('--env=%s', $input->getOption('env'));
        }

        if ($input->hasOption('timeout')) {
            $command[] = sprintf('--timeout=%d', $input->getOption('timeout'));
        }

        $this->assetsCommandProcess = new Process(
            $command,
            realpath($this->getContainer()->getParameter('kernel.project_dir'))
        );
        $this->assetsCommandProcess->setTimeout(null);
        $this->assetsCommandProcess->start();
    }

    private function getBuildAssetsProcessExitCode(OutputInterface $output): ?int
    {
        if (!$this->assetsCommandProcess) {
            return 0;
        }

        if (!$this->assetsCommandProcess->isTerminated()) {
            $this->assetsCommandProcess->wait();
        }

        if ($this->assetsCommandProcess->isSuccessful()) {
            $output->writeln('Assets has been installed successfully');
            $output->writeln($this->assetsCommandProcess->getOutput());
        } else {
            $output->writeln($this->assetsCommandProcess->getOutput());
            $output->writeln('Assets has not been installed! Please run "php bin/console oro:assets:install".');
            $output->writeln('Error during install assets:');
            $output->writeln($this->assetsCommandProcess->getErrorOutput());
        }

        return $this->assetsCommandProcess->getExitCode();
    }
}
