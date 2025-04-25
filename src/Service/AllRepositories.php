<?php

namespace App\Service;

use App\Entity\Asn;
use App\Repository\AsnRepository;
use App\Repository\DistrictRepository;
use App\Repository\GroupeRepository;
use App\Repository\RegionRepository;
use App\Repository\ScoutRepository;

class AllRepositories
{
    public const GROUPE = 'GROUPE';
    public const DISTRICT = 'DISTRICT';
    public const REGION = 'REGION';
    public const ASN = 'ASN';
    public const TELEPHONE = 'TELEPHONE';
    public const MATRICULE = 'MATRICULE';
    public const CODE = 'CODE';


    public function __construct(
        private AsnRepository $asnRepository,
        private RegionRepository $regionRepository,
        private DistrictRepository $districtRepository,
        private GroupeRepository $groupeRepository,
        private ScoutRepository $scoutRepository
    )
    {
    }

    /**
     * @param int|null $id
     * @param string|null $sigle
     * @return object|null
     */
    public function getOneAsn(?int $id = null, ?string $sigle = null): object|null
    {
        return match(true){
            !is_null($id) => $this->asnRepository->findOneBy(['id' => $id]),
            !is_null($sigle) => $this->asnRepository->findOneBy(['sigle' => $sigle]),
            default => null,
        };
    }

    /**
     * Liste des ASN
     * @return array
     */
    public function getAllAsn(): array
    {
        return $this->asnRepository->findAll();
    }

    public function getOneRegion(?int $id)
    {
        return $this->regionRepository->findOneBy(['id' => $id]);
    }

    public function getAllRegion()
    {
        return $this->regionRepository->findAll();
    }

    public function getOneDistrict(?int $id)
    {
        return $this->districtRepository->findOneBy(['id' => $id]);
    }

    public function getDistrictsByRegionId(?int $id)
    {
        return $this->districtRepository->findBy(['region' => $id], ['nom' => "ASC"]);
    }

    public function getAllDistrict()
    {
        return $this->districtRepository->findAll();
    }

    public function getOneGroupe(?int $id)
    {
        return $this->groupeRepository->findOneBy(['id' => $id]);
    }

    public function getAllGroupeOrByQuery(?int $district = null, ?int $region = null)
    {
        return match (true) {
            !is_null($district) => $this->groupeRepository->findByQuery($district),
            !is_null($region) => $this->groupeRepository->findByQuery(null, $region),
            default => $this->groupeRepository->findAll()
        };
    }

    public function getOneScout(?int $id = null, ?string $code = null, ?string $matricule= null)
    {
        return match (true){
            !is_null($id) => $this->scoutRepository->findOneScout($id),
            !is_null($code) => $this->scoutRepository->findOneScout(null, $code),
            !is_null($matricule) => $this->scoutRepository->findOneScout(null, null, $matricule),
        };
    }


    public function getAllScoutOrByQuery($variable = null, ?string $type = 'ALL')
    {
        return match ($type){
            self::GROUPE => $this->scoutRepository->findAllByGroup($variable),
            self::DISTRICT => $this->scoutRepository->findAllByDistrict($variable),
            self::REGION => $this->scoutRepository->findAllByRegion($variable),
            self::ASN => $this->scoutRepository->findAllByAsn($variable),
            self::TELEPHONE => $this->scoutRepository->findAllByTelephone($variable),
            default => $this->scoutRepository->findAllScout(),
        };
    }
}