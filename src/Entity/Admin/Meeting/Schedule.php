<?php

namespace App\Entity\Admin\Meeting;

/**
 * @ORM\Table(name="adm_meeting_schedules")
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
}