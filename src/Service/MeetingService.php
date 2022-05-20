<?php

namespace App\Service;

use App\Entity\Meeting;

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
        // Do extra stuff on the Meeting entity here, like setting payment data
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
        // Do extra stuff on the Meeting entity here, like setting the packed item amount
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
            // Throw a custom exception here and handle this in your controller,
            // to show an error message to the user
            throw new MeetingStateException(sprintf('Cannot change the state of the Meeting, because %s', $e->getMessage()), 0, $e);
        }
    }
}