<?php

namespace App\Repository;

use App\Entity\Groupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Groupe>
 */
class GroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupe::class);
    }

    public function findByQuery(?int $district = null, ?int $region = null)
    {
        $query = $this->query();
        if ($district){
            $query->where('d.id = :district')
                ->setParameter('district', $district);
        }

        if ($region){
            $query->where('r.id = :region')
                ->setParameter('region', $region);
        }

        return $query->getQuery()->getResult();
    }

    public function query(): QueryBuilder
    {
        return $this->createQueryBuilder('g')
            ->addSelect('d')
            ->addSelect('r')
            ->addSelect('a')
            ->leftJoin('g.district', 'd')
            ->leftJoin('d.region', 'r')
            ->leftJoin('r.asn', 'a');
    }

    //    /**
    //     * @return Groupe[] Returns an array of Groupe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Groupe
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
