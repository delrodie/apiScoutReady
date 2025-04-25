<?php

namespace App\Repository;

use App\Entity\Scout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Scout>
 */
class ScoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scout::class);
    }

    public function findOneScout(
        ?int $id = null,
        ?string $code = null,
        ?string $matricule = null
    )
    {
        $query = $this->query();
        if ($id){
            $query->where('s.id = :id')
                ->setParameter('id', $id);
        }

        if ($code){
            $query->where('s.code = :code')
                ->setParameter('code', $code);
        }

        if ($matricule){
            $query->where('s.matricule = :matricule')
                ->setParameter('matricule', $matricule);
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    public function findAllScout()
    {
        return $this->query()
            ->orderBy('s.nom', "ASC")
                    ->addOrderBy('s.prenom', "ASC")
                    ->getQuery()->getResult();
    }

    public function findAllByGroup(?int $group)
    {
        return $this->query()
            ->where('g.id = :group')
            ->setParameter('group', $group)
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findAllByDistrict(?int $district)
    {
        return $this->query()
            ->where('d.id = :district')
            ->setParameter('district', $district)
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findAllByRegion(?int $region)
    {
        return $this->query()
            ->where('r.id = :region')
            ->setParameter('region', $region)
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findAllByAsn(?int $asn)
    {
        return $this->query()
            ->where('a.id = :asn')
            ->setParameter('asn', $asn)
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findAllByTelephone(?string $telephone)
    {
        return $this->query()
            ->where('s.telephone = :telephone')
            ->setParameter('telephone', $telephone)
            ->orderBy('s.dateNaissance', 'ASC')
            ->getQuery()->getResult();
    }

    public function query()
    {
        return $this->createQueryBuilder('s')
            ->addSelect('g')
            ->addSelect('d')
            ->addSelect('r')
            ->addSelect('a')
            ->leftJoin('s.groupe', 'g')
            ->leftJoin('g.district', 'd')
            ->leftJoin('d.region', 'r')
            ->leftJoin('r.asn', 'a');
    }

    //    /**
    //     * @return Scout[] Returns an array of Scout objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Scout
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
