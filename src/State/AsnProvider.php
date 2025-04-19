<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\AsnOutput;
use App\Service\AllRepositories;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AsnProvider implements ProviderInterface
{
    public function __construct(private AllRepositories $allRepositories)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!empty($uriVariables['id'])){
            $asn = $this->allRepositories->getOneAsn($uriVariables['id']);
            if (!$asn){
                throw new NotFoundHttpException("Aucune ASN touvée avec l'identifiant: {$uriVariables['id']}.");
            }
            return AsnOutput::mapToOutput($asn);
        }

        $asns = $this->allRepositories->getAllAsn();
        return array_map([AsnOutput::class, 'mapToOutput'], $asns);
    }



}
