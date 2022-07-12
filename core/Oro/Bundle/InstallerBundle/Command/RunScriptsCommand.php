<?php
declare(strict_types=1);

namespace Oro\Bundle\InstallerBundle\Command;

use Oro\Bundle\InstallerBundle\CommandExecutor;
use Oro\Bundle\InstallerBundle\ScriptExecutor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Runs OroScript files in the application scope.
 */
class RunScriptsCommand extends ContainerAwareCommand
{
    /** @var string */
    protected static $defaultName = 'oro:platform:run-script';

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function configure()
    {
        $this
            ->addArgument('script', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Script files')
            ->setDescription('Runs OroScript files in the application scope.')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command runs OroScript files while providing them with
the container and command runner instance references.

  <info>php %command.full_name% <script1> <script2> <scriptN></info>

HELP
            )
        ;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandExecutor = new CommandExecutor(
            $input->hasOption('env') ? $input->getOption('env') : null,
            $output,
            $this->getApplication(),
            $this->getContainer()->get('oro_cache.oro_data_cache_manager')
        );
        $scriptExecutor = new ScriptExecutor(
            $output,
            $this->getContainer(),
            $commandExecutor
        );
        $scriptFiles = $input->getArgument('script');
        foreach ($scriptFiles as $scriptFile) {
            $scriptExecutor->runScript($scriptFile);
        }
    }
}
