<?php

namespace BeyondCapable\Domain\Entity\Admin\Meeting;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="adm_meeting_requests")
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
}
