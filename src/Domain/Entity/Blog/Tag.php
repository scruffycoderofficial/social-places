<?php

namespace BeyondCapable\Domain\Entity\Blog;

use Doctrine\ORM\Mapping as ORM;
use BeyondCapable\Domain\Entity\Repository\Blog\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="blog_post_tags")
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */

    private ?string $name = null;

    /**
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="tags")
     */
    private $posts;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
