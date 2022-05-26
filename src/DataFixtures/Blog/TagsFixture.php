<?php

namespace BeyondCapable\DataFixtures\Blog;

use BeyondCapable\Entity\Blog\Tag;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TagsFixture extends Fixture
{
    /**
     * Number of comments to add by post
     */
    public const NUMBER_OF_TAGS = 5;

    public function load(ObjectManager $manager): void
    {
        $tags = [];

        for ($i = 1; $i <= self::NUMBER_OF_TAGS; $i++) {
            $tag = new Tag();

            $tag->setName(sprintf("tag %d", $i));

            $tags[] = $tag;
        }

        $posts = $manager->getRepository('App\Entity\Blog\Post')->findAll();

        $tagsToAdd = 1;

        foreach ($posts as $post) {
            for ($j = 0; $j < $tagsToAdd; $j++) {
                $post->addTag($tags[$j]);
            }

            $tagsToAdd = $tagsToAdd % 5 + 1;
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return ['App\DataFixtures\Blog\PostsFixture'];
    }
}
