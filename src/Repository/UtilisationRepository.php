<?php

namespace App\Repository;

use ApiPlatform\OpenApi\Model\Parameter;
use App\Entity\Utilisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisation>
 */
class UtilisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisation::class);
    }

    public function findByTelephone(string $telephone, string $annee)
    {
        return $this->query()
            ->where('s.telephone =: telephone')
            ->andWhere('u.annee = :annee')
            ->orderBy('s.dateNaissance', 'ASC')
            ->setParameters(new ArrayCollection([
                'telephone' => $telephone,
                'annee' => $annee
            ]))
            ->getQuery()->getResult();
    }

    public function findByCode(string $code, string $annee)
    {
        return $this->query()
            ->where('s.code = :code')
            ->andWhere('u.annee = :annee')
            ->setParameters(new ArrayCollection(array(
                new Parameter('code', $code),
                new Parameter('annee', $annee)
            )))
            ->getQuery()->getOneOrNullResult();
    }

    public function findByMatricule(string $matricule, string $annee)
    {
        return $this->query()
            ->where('s.matricule = :matricule')
            ->andWhere('u.annee = :annee')
            ->setParameters(new ArrayCollection(array(
                new Parameter('matricule', $matricule),
                new Parameter ('annee', $annee)
            )))
            ->getQuery()->getOneOrNullResult();
    }

    public function findByGroupe(int $groupe, string $annee)
    {
        return $this->query()
            ->where('g.id = :groupe')
            ->andWhere('u.annee = :annee')
            ->setParameters(new ArrayCollection(array(
                new Parameter('groupe', $groupe),
                new Parameter('annee', $annee)
            )))
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->getQuery()->getResult();
    }

    public function findByDistrict(int $distrcit, string $annee)
    {
        return $this->query()
            ->where('d.id = :district')
            ->andWhere('u.annee = :annee')
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->setParameters(new ArrayCollection(array(
                new Parameter('district', $distrcit),
                new Parameter('annee', $annee)
            )))
            ->getQuery()->getResult();
    }

    public function findByregion(int $region, string $annee)
    {
        return $this->query()
            ->where('r.id = :region')
            ->andWhere('u.annee = :annee')
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->setParameters(new ArrayCollection([
                new Parameter('region', $region),
                new Parameter('annee', $annee)
            ]))
            ->getQuery()->getResult();
    }

    public function findByAsn(int $asn, string $annee)
    {
        return $this->query()
            ->where('a.id = :asn')
            ->andWhere('u.annee = :annee')
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->setParameters(new ArrayCollection([
                new Parameter('asn', $asn),
                new Parameter('annee', $annee
                )
            ]))
            ->getQuery()->getResult();
    }

    public function findByPage(string $annee)
    {
        return $this->query()
            ->where('u.annee = :annee')
            ->orderBy('s.nom', 'ASC')
            ->addOrderBy('s.prenom', 'ASC')
            ->setParameter('annee', $annee)
            ->getQuery()->getResult();
    }

    public function findDifferentFromStatus(int $id, ?int $statut, ?string $annee)
    {
        return $this->query()
            ->where('u.id = :id')
            ->andWhere('u.statut <> :statut')
            ->andWhere('u.annee = :annee')
            ->setParameter('id', $id)
            ->setParameter('statut', $statut)
            ->setParameter('annee', $annee)
            ->getQuery()->getOneOrNullResult();
    }

    public function findByScoutFromStatus(int $scout, string $annee, int $valid, int $attente)
    {
        return $this->query()
            ->where('s.id = :scout')
            ->andWhere('u.annee = :annee')
            ->andWhere('u.statut = :valid OR u.statut = :attente')
//            ->orWhere('u.statut = :attente')
//            ->setParameters(new ArrayCollection([
//                new Parameter('scout', $scout),
//                new Parameter('annee', $annee),
//                new Parameter('valid', $valid),
//                new Parameter('attente', $attente)
//            ]))
            ->setParameter('scout', $scout)
            ->setParameter('annee', $annee)
            ->setParameter('valid', $valid)
            ->setParameter('attente', $attente)
            ->getQuery()->getOneOrNullResult();
    }

    public function query()
    {
        return $this->createQueryBuilder('u')
            ->addSelect('s')
            ->addSelect('g')
            ->addSelect('d')
            ->addSelect('r')
            ->addSelect('a')
            ->leftJoin('u.scout', 's')
            ->leftJoin('u.groupe', 'g')
            ->leftJoin('g.district', 'd')
            ->leftJoin('d.region', 'r')
            ->leftJoin('r.asn', 'a')
            ;
    }

    //    /**
    //     * @return Utilisation[] Returns an array of Utilisation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Utilisation
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
