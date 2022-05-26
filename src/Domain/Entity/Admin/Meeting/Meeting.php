<?php

namespace BeyondCapable\Domain\Entity\Admin\Meeting;

use Doctrine\ORM\Mapping as ORM;
use BeyondCapable\Domain\Entity\Repository\Admin\Meeting\MeetingRepository;

/**
 * @ORM\Table(name="adm_meetings")
 * @ORM\Entity(repositoryClass=MeetingRepository::class)
 */
class Meeting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    /**
     * @ORM\Column(type="text")
     */
    private $purpose;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status, $context = []): void
    {
        $this->status= $status;
    }
}
