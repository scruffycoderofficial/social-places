<?php

namespace Oro\Bundle\ApiBundle\Tests\Functional\Environment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\TestFrameworkBundle\Entity\TestFrameworkEntityInterface;

/**
 * @ORM\Table(name="test_api_override_target",
 *      indexes={
 *          @ORM\Index(name="test_api_override_t_name_idx", columns={"name"})
 *     }
 * )
 * @ORM\Entity
 * @Config
 */
class TestOverrideClassTarget implements TestFrameworkEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    public $name;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="TestOverrideClassOwner", mappedBy="targets")
     * @ORM\JoinTable(name="test_api_override_rel_targets",
     *     joinColumns={@ORM\JoinColumn(name="owner_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="target_id", referencedColumnName="id")}
     * )
     */
    protected $owners;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
    }

    /**
     * @return Collection|TestOverrideClassOwner[]
     */
    public function getOwners()
    {
        return $this->owners;
    }

    /**
     * @param TestOverrideClassOwner $owner
     */
    public function addOwner(TestOverrideClassOwner $owner)
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
            $owner->addTarget($this);
        }
    }

    /**
     * @param TestOverrideClassOwner $owner
     */
    public function removeOwner(TestOverrideClassOwner $owner)
    {
        if ($this->owners->contains($owner)) {
            $this->owners->removeElement($owner);
            $owner->removeTarget($this);
        }
    }
}
