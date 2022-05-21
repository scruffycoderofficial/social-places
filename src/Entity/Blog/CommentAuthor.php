<?php

namespace App\Entity\Blog;

/**
 * @package App\Entity
 */
class CommentAuthor extends Author
{
    /**
     * @OneToMany(targetEntity="Comment",mappedBy="commentAuthor")
     */
    protected $comments;
}