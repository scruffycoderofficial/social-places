<?php

namespace BeyondCapable\Domain\Entity\Blog;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blog_post_comment_authors")
 * @ORM\Entity
 */
class CommentAuthor extends Author
{
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="author")
     */
    protected $comments;
}
