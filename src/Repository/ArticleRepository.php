<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Search for articles by reference.
     *
     * @param string|null $reference The reference to search for.
     * @return Article[] Returns an array of Article objects.
     */
    public function searchByReference(?string $reference): array
    {
        $qb = $this->createQueryBuilder('a');

        if ($reference) {
            $qb->where('a.reference LIKE :reference')
               ->setParameter('reference', '%' . $reference . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find articles with quantity below their minimum level (`niveauMin`).
     *
     * @return Article[] Returns an array of Article objects.
     */
    public function findLowStockArticles(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.quantite < a.niveauMin')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find an article by its ID.
     *
     * @param int $id The ID of the article.
     * @return Article|null Returns the Article object or null if not found.
     */
    public function findArticleById(int $id): ?Article
    {
        return $this->find($id);
    }
}