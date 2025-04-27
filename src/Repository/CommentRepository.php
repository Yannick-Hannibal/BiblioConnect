<?php 

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Récupère les commentaires approuvés pour un livre donné
     */
    public function findApprovedCommentsForBook($book): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.book = :book')
            ->andWhere('c.status = :status')
            ->setParameter('book', $book)
            ->setParameter('status', 'approved')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
