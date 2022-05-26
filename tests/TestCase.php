<?php

namespace App\Tests;

use App\Entity\Admin\Meeting;
use App\Entity\Admin\User;
use App\Entity\Admin\Contact;
use App\Entity\Blog\Author;
use App\Entity\Blog\Comment;
use App\Entity\Blog\CommentAuthor;
use App\Entity\Blog\Post;
use App\Entity\Blog\PostAuthor;
use App\Entity\Blog\Tag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use App\Tests\Concern\InteractsWithDatabase;

abstract class TestCase extends FixtureAwareTestCase
{
    use InteractsWithDatabase;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var SchemaTool
     */
    protected $schemaTool;

    /**
     * @var array
     */
    private $entityClasses = [];

    /**
     * @throws ToolsException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $this->schemaTool = $this->getSchemaTool($this->entityManager);

        /**
         * We expect to have two database tables here contacts and users respectively
         */
        $this->entityClasses = $this->getClassMetadataCollection($this->entityManager, [
            Contact::class,
            Meeting::class,
            User::class,

            Comment::class,
            CommentAuthor::class,
            Post::class,
            PostAuthor::class,
            Tag::class,
        ]);

        $this->createTables($this->schemaTool, $this->entityClasses);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropTables($this->schemaTool, $this->entityClasses);
    }
}
