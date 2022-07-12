<?php

namespace Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\AssertTrait;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class ConsoleContext extends RawMinkContext implements KernelAwareContext
{
    use AssertTrait, KernelDictionary;

    /**
     * Runs Symfony console command
     *
     * Example: And I run Symfony "cache:clear" command
     * Example: And I run Symfony "cache:clear" command in "prod" environment
     *
     * @Given /^(?:|I )run Symfony "(?P<command>[^\"]+)" command$/
     * @Given /^(?:|I )run Symfony "(?P<command>[^\"]+)" command in "(?P<env>[^\"]+)" environment$/
     *
     * @param string $command
     * @param string $env
     */
    public function iRunSymfonyConsoleCommand(string $command, string $env = 'prod'): void
    {
        $process = $this->runCommand($command, '--env=' . $env);

        self::assertTrue(
            $process->isSuccessful(),
            sprintf(
                'Command "%s" unsuccessful. Command output: %s',
                $command,
                $process->getErrorOutput()
            )
        );
    }

    /**
     * Runs Symfony console command with arguments
     *
     * Example: And I run Symfony "cache:clear" command with arguments "--env=prod"
     *
     * @Given /^(?:|I )run Symfony "(?P<command>[^\"]+)" command with arguments "(?P<args>[^\"]+)"$/
     *
     * @param string $command
     * @param string $args
     */
    public function iRunSymfonyConsoleCommandWithArgs(string $command, string $args): void
    {
        $process = $this->runCommand($command, $args);

        self::assertTrue(
            $process->isSuccessful(),
            sprintf(
                'Command "%s" unsuccessful. Command output: %s',
                $command,
                $process->getErrorOutput()
            )
        );
    }

    private function runCommand(string $command, string $args): Process
    {
        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        self::assertNotFalse($phpPath, 'The PHP executable cannot be found');

        $projectDir = realpath($this->getContainer()->getParameter('kernel.project_dir'));

        $process = Process::fromShellCommandline(
            sprintf(
                '%s bin/console %s %s',
                $phpPath,
                $command,
                $args
            ),
            $projectDir
        );
        $process->run();

        return $process;
    }
}
