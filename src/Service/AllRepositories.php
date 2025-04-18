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
}