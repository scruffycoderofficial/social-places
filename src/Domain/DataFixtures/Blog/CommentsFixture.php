<?php

namespace BeyondCapable\Domain\DataFixtures\Blog;

use DateTime;
use Exception;
use BeyondCapable\Domain\Entity\Blog\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CommentsFixture extends Fixture
{
    /**
     * Number of comments to add by post
     */
    public const NUMBER_OF_COMMENTS_BY_POST = 5;

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $posts = $manager->getRepository('App\Entity\Blog\Post')->findAll();

        foreach ($posts as $post) {
            for ($i = 1; $i <= self::NUMBER_OF_COMMENTS_BY_POST; $i++) {
                $comment = new Comment();

                $comment->setBody('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
                $comment->setPublicationDate(new DateTime(sprintf('-%d days', self::NUMBER_OF_COMMENTS_BY_POST - $i)));
                $comment->setPost($post);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return ['App\DataFixtures\Blog\PostsFixture'];
    }
}
