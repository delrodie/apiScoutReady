<?php

namespace App\Service;

use App\Entity\Asn;
use App\Repository\ApiClientRepository;
use App\Repository\AsnRepository;
use App\Repository\DistrictRepository;
use App\Repository\GroupeRepository;
use App\Repository\RegionRepository;
use App\Repository\ScoutRepository;
use App\Repository\UtilisationRepository;
use App\Service\Gestion;

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
        private AsnRepository         $asnRepository,
        private RegionRepository      $regionRepository,
        private DistrictRepository    $districtRepository,
        private GroupeRepository      $groupeRepository,
        private ScoutRepository       $scoutRepository,
        private ApiClientRepository   $apiClientRepository,
        private UtilisationRepository $utilisationRepository,
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

    public function getOneDistrict($value, $type)
    {
        return match ($type){
            'ID' => $this->districtRepository->findOneBy(['id' => $value]),
            'NOM' => $this->districtRepository->findOneBy(['nom' => $value]),
        };
    }

    public function getDistrictsByRegionId(?int $id)
    {
        return $this->districtRepository->findBy(['region' => $id], ['nom' => "ASC"]);
    }

    public function getAllDistrict()
    {
        return $this->districtRepository->findAll();
    }

    public function getOneGroupe($value, $type = 'ID')
    {
        return match ($type){
            'ID' => $this->groupeRepository->findOneBy(['id' => $value]),
            'PAROISSE' => $this->groupeRepository->findOneBy(['paroisse' => $value])
        };
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

    public function getOneScoutByTelephone(?string $telephone)
    {
        return $this->scoutRepository->findOneBy([
            'telephone' => $telephone,
            'telephoneParent' => false,
        ], ['id' => 'DESC']);
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

    public function getOneClient($variable, ?String $type)
    {
        return match ($type){
            'ID' => $this->apiClientRepository->findOneBy(['id' => $variable]),
            'NAME' => $this->apiClientRepository->findOneBy(['name' => $variable]),
            'KEY' => $this->apiClientRepository->findOneBy(['apiKey' => $variable]),
        };
    }

    public function getAllClients()
    {
        return $this->apiClientRepository->findAll();
    }

    public function getOneUtilisation(?int $scout)
    {
        return $this->utilisationRepository->findOneBy([
            'scout' => $scout,
            'annee' => Gestion::annee(),
            'statut' => Gestion::UTILISATEUR_STATUT_APPROUVE
        ]);
    }

    public function getUtilisateurByScout(?int $scout)
    {
        return $this->utilisationRepository->findOneBy(['scout' => $scout], ['id' => 'DESC']);
    }
}