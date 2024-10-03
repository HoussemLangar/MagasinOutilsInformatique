<?php

namespace App\Repository;

use App\Entity\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    public function findBySearchTerm(string $searchTerm)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.article', 'a')
            ->andWhere('a.reference LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByDate(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.dateSortie = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findByCriteria(\DateTimeInterface $date, ?string $operationType, ?array $articles): array
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.dateSortie = :date')
            ->setParameter('date', $date);
    
        if ($operationType) {
            $qb->andWhere('o.type = :operationType')
               ->setParameter('operationType', $operationType);
        }
    
        if ($articles) {
            $qb->andWhere('o.article IN (:articles)')
               ->setParameter('articles', $articles);
        }
    
        return $qb->getQuery()->getResult();
    }
   
}
