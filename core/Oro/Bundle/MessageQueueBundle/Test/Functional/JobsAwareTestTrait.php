<?php

namespace Oro\Bundle\MessageQueueBundle\Test\Functional;

use Oro\Bundle\MessageQueueBundle\Job\JobManager;
use Oro\Component\MessageQueue\Job\Job;
use Oro\Component\MessageQueue\Job\JobProcessor;
use Oro\Component\MessageQueue\Job\JobRunner;

/**
 * Provides useful assertion methods for the jobs management related functional tests.
 * It is expected that this trait will be used in classes that have "getContainer" static method.
 * E.g. classes derived from Oro\Bundle\TestFrameworkBundle\Test\WebTestCase.
 */
trait JobsAwareTestTrait
{
    /**
     * @return JobManager
     */
    protected function getJobManager(): JobManager
    {
        return $this->getContainer()->get('oro_message_queue.job.manager');
    }

    /**
     * @return JobRunner
     */
    protected function getJobRunner(): JobRunner
    {
        return $this->getContainer()->get('oro_message_queue.job.runner');
    }

    /**
     * @return JobProcessor
     */
    protected function getJobProcessor(): JobProcessor
    {
        return $this->getContainer()->get('oro_message_queue.job.processor');
    }

    /**
     * @return Job|null
     */
    protected function createUniqueJob(): ?Job
    {
        $ownerId = $this->getUniqid();
        $jobName = $this->getUniqid();
        $rootJob = $this->getJobProcessor()->findOrCreateRootJob($ownerId, $jobName, true);
        if (!$rootJob) {
            return null;
        }

        $childJob = $this->getJobProcessor()->findOrCreateChildJob($jobName, $rootJob);
        $this->getJobProcessor()->startChildJob($childJob);

        return $childJob;
    }

    /**
     * @param Job|null $rootJob
     * @return Job
     */
    protected function createDelayedJob(Job $rootJob = null): Job
    {
        if (!$rootJob) {
            $rootJob = $this->createUniqueJob();
        }

        return $this->getJobProcessor()->findOrCreateChildJob($this->getUniqid(), $rootJob);
    }

    /**
     * @param int $jobId
     * @return array
     */
    protected function getDependentJobsByJobId(int $jobId): array
    {
        $job = $this->getJobProcessor()->findJobById($jobId);
        $rootJob = $job->getRootJob() ?: $job;

        return $rootJob->getData()['dependentJobs'] ?? [];
    }

    /**
     * @return string
     */
    protected function getUniqid(): string
    {
        return uniqid(microtime(true), true);
    }
}
