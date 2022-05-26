<?php

namespace App\Entity\Blog;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="blog_post_authors")
 * @ORM\Entity
 */
class PostAuthor extends Author
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $bio;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="author")
     */
    protected $posts;

    /**
     * Initializes collections
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @param string $bio
     */
    public function setBio(string $bio): void
    {
        $this->bio = $bio;
    }

    /**
     * @return string
     */
    public function getBio(): string
    {
        return $this->bio;
    }

    /**
     * @param ArrayCollection $posts
     */
    public function setPosts(ArrayCollection $posts): void
    {
        $this->posts = $posts;
    }

    /**
     * @return ArrayCollection
     */
    public function getPosts(): ArrayCollection
    {
        return $this->posts;
    }
}
