<?php

namespace App\Service;

use App\Entity\Asn;
use App\Repository\AsnRepository;

class AllRepositories
{
    public function __construct(
        private AsnRepository $asnRepository,
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
}