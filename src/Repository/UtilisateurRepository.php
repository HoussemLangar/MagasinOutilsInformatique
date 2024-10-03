<?php
namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function findBySearchQuery(string $query): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.Nom LIKE :query')
            ->orWhere('u.Email LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('u.Id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}


