<?php
namespace App\Repository;

use App\Entity\Rapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rapport::class);
    }

    public function findOperationsByDate(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.dateJour = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}

