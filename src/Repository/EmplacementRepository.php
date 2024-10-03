<?php

namespace App\Repository;

use App\Entity\Emplacement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emplacement>
 */
class EmplacementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emplacement::class);
    }

    /**
     * Rechercher des emplacements par description
     *
     * @param string $description
     * @return Emplacement[]
     */
    public function findByDescription(string $description): array
{
    return $this->createQueryBuilder('e')
        ->where('e.Description LIKE :description')
        ->setParameter('description', '%' . $description . '%')
        ->getQuery()
        ->getResult();
}
}
