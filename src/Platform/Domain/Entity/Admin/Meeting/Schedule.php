<?php

namespace BeyondCapable\Platform\Domain\Entity\Admin\Meeting;

use Doctrine\ORM\Mapping as ORM;

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
