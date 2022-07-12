<?php
declare(strict_types=1);

namespace Oro\Bundle\ImapBundle\Command\Cron;

use Oro\Bundle\CronBundle\Command\CronCommandInterface;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureChecker;
use Oro\Bundle\ImapBundle\OriginSyncCredentials\SyncCredentialsIssueManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sends notifications if email origin sync failed due to invalid credentials.
 */
class SendCredentialNotificationsCommand extends Command implements CronCommandInterface
{
    /** @var string */
    protected static $defaultName = 'oro:cron:imap-credential-notifications';

    private FeatureChecker $featureChecker;
    private SyncCredentialsIssueManager $syncCredentialsIssueManager;

    public function __construct(
        FeatureChecker $featureChecker,
        SyncCredentialsIssueManager $syncCredentialsIssueManager
    ) {
        parent::__construct();

        $this->featureChecker = $featureChecker;
        $this->syncCredentialsIssueManager = $syncCredentialsIssueManager;
    }

    public function getDefaultDefinition()
    {
        return '0 4 * * *';
    }

    public function isActive()
    {
        return $this->featureChecker->isResourceEnabled(self::getDefaultName(), 'cron_jobs');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function configure()
    {
        $this
            ->setDescription('Sends notifications if email origin sync failed due to invalid credentials.')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command sends notifications
if email origin sync failed due to invalid credentials.

  <info>php %command.full_name%</info>

HELP
            )
        ;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Process the invalid credentials origins</info>');
        $processedOrigins = $this->syncCredentialsIssueManager->processInvalidOrigins();
        if (count($processedOrigins)) {
            $output->writeln('<info>Processed origins:</info>', OutputInterface::VERBOSITY_DEBUG);
            foreach ($processedOrigins as $processedOrigin) {
                $output->writeln(
                    sprintf(
                        '<comment>id: %s, username: %s, host: %s</comment>',
                        $processedOrigin->getId(),
                        $processedOrigin->getUser(),
                        $processedOrigin->getImapHost()
                    ),
                    OutputInterface::VERBOSITY_DEBUG
                );
            }
        } else {
            $output->writeln(
                '<info>Invalid credentials origins was not found</info>',
                OutputInterface::VERBOSITY_DEBUG
            );
        }

        return 0;
    }
}
