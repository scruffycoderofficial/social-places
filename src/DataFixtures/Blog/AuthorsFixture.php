<?php

namespace App\DataFixtures\Blog;

use DateTime;
use App\Entity\Blog\Post;
use App\Entity\Blog\Comment;
use App\Entity\Blog\PostAuthor;
use App\Entity\Blog\CommentAuthor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AuthorsFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $postAuthor = new PostAuthor();

        $postAuthor->setName('George Abitbol');
        $postAuthor->setEmail('gabitbol@example.com');
        $postAuthor->setBio('L\'homme le plus classe du monde');

        $manager->persist($postAuthor);

        $post = new Post();

        $post->setTitle('My post');
        $post->setBody('Lorem ipsum');
        $post->setPublicationDate(new DateTime());
        $post->setAuthor($postAuthor);

        $manager->persist($post);

        $commentAuthor = new CommentAuthor();
        $commentAuthor->setName('Kévin Dunglas');

        $commentAuthor->setEmail('dunglas@gmail.com');
        $manager->persist($commentAuthor);

        $comment = new Comment();

        $comment->setBody('My comment');
        $comment->setAuthor($commentAuthor);
        $comment->setPublicationDate(new DateTime());
        $post->addComment($comment);
        $manager->persist($comment);

        $manager->flush();
    }
}
