<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Person
 *
 * @package BeyondCapable\Core\Platform\Domain\Entity
 *
 * @ORM\Table(name="cmn_persons")
 * @ORM\Entity()
 */
class Person
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=250)
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=250)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email_address", type="string", length=100)
     */
    private $emailAddress;


    /**
     * @var string|null
     *
     * @ORM\Column(name="phone_number", type="string", length=50)
     */
    private $phoneNumber;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return Person
     */
    public function setFirstName(?string $firstName): Person
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return Person
     */
    public function setLastName(?string $lastName): Person
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    /**
     * @param string|null $emailAddress
     * @return Person
     */
    public function setEmailAddress(?string $emailAddress): Person
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return Person
     */
    public function setPhoneNumber(?string $phoneNumber): Person
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}