<?php

namespace App\DataFixtures\Blog;

use DateTime;
use Exception;
use App\Entity\Blog\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostsFixture extends Fixture
{
    /**
     * Number of posts to add
     */
    public const NUMBER_OF_POSTS = 10;

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= self::NUMBER_OF_POSTS; $i++) {
            $post = new Post();

            $post->setTitle(sprintf('Blog post number %d', $i));
            $post->setBody('Lorem ipsum dolor sit amet, consectetur adipiscing elit');
            $post->setPublicationDate(new DateTime(sprintf('-%d days', self::NUMBER_OF_POSTS - $i)));

            $manager->persist($post);
        }

        $manager->flush();
    }
}
