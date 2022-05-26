<?php

namespace BeyondCapable\Domain\Entity\Repository\Blog;

use BeyondCapable\Domain\Entity\Blog\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a post with its comments
     *
     * @param $id
     * @return int|mixed|string|null
     */
    public function findWithComments($id): mixed
    {
        $result = null;

        try {
            $result = $this
                ->createQueryBuilder('p')
                ->addSelect('c')
                ->leftJoin('p.comments', 'c')
                ->where('p.id = :id')
                ->orderBy('c.publicationDate', 'ASC')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // Log issues here
        }

        return $result;
    }

    public function findWithCommentCount()
    {
        return $this
            ->createQueryBuilder('p')
            ->leftJoin('p.comments', 'c')
            ->addSelect('COUNT(c.id)')
            ->groupBy('p.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds posts having tags
     *
     * @param string[] $tagNames
     * @return Post[]
     */
    public function findHavingTags(array $tagNames): array
    {
        return $queryBuilder = $this
            ->createQueryBuilder('p')
            ->addSelect('t')
            ->join('p.tags', 't')
            ->where('t.name IN (:tagNames)')
            ->groupBy('p.id')
            ->having('COUNT(t.name) >= :numberOfTags')
            ->setParameter('tagNames', $tagNames)
            ->setParameter(
                'numberOfTags',
                count($tagNames)
            )
            ->getQuery()
            ->getResult()
            ;
    }
}
