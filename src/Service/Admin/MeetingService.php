<?php

namespace App\Service\Admin;

use App\Entity\Admin\Meeting;

use App\Exception\MeetingStateException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\Workflow\Exception\LogicException;

/**
 * Class MeetingService
 *
 * @package App\Service
 */
class MeetingService
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var WorkflowInterface $MeetingWorkflow
     */
    private $MeetingWorkflow;

    /**
     * MeetingService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param WorkflowInterface $MeetingWorkflow
     */
    public function __construct(EntityManagerInterface $entityManager, WorkflowInterface $MeetingWorkflow)
    {
        $this->entityManager = $entityManager;
        $this->MeetingWorkflow = $MeetingWorkflow;
    }

    /**
     * @param Meeting $Meeting
     */
    public function approve(Meeting $Meeting): void
    {
        try {
            $this->doTransition('approve', $Meeting);
        } catch (MeetingStateException $e) {
        }

        $this->entityManager->flush();
    }

    /**
     * @param Meeting $Meeting
     */
    public function reject(Meeting $Meeting): void
    {
        try {
            $this->doTransition('reject', $Meeting);
        } catch (MeetingStateException $e) {
        }

        $this->entityManager->flush();
    }

    /**
     * @param Meeting $Meeting
     */
    public function cancel(Meeting $Meeting): void
    {
        try {
            $this->doTransition('cancel', $Meeting);
        } catch (MeetingStateException $e) {
        }

        $this->entityManager->flush();
    }

    /**
     * @param string $transition
     * @param Meeting $Meeting
     * @throws MeetingStateException
     */
    private function doTransition(string $transition, Meeting $Meeting): void
    {
        try {
            $this->MeetingWorkflow->apply($Meeting, $transition);
        } catch (LogicException $e) {
            throw new MeetingStateException(sprintf('Cannot change the state of the Meeting, because %s', $e->getMessage()), 0, $e);
        }
    }
}
