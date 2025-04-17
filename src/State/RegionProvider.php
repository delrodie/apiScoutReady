<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\RegionOutput;
use App\Service\AllRepositories;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegionProvider implements ProviderInterface
{
    public function __construct(private AllRepositories $allRepositories)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!empty($uriVariables['id'])){
            $region = $this->allRepositories->getOneRegion($uriVariables['id']);
            if (!$region) throw  new NotFoundHttpException("Aucune region trouvée avec l'identifiant {$uriVariables['id']}.");

            return $this->mapToOut($region);
        }

        $regions = $this->allRepositories->getAllRegion();

        return array_map([$this, 'mapToOut'], $regions);
    }

    private function mapToOut($region): RegionOutput
    {
        $dto = new RegionOutput();
        $dto->id = $region->getId();
        $dto->nom = $region->getNom();
        $dto->symbolique = $region->getSymbolique();
        $dto->asn = $region->getAsn();

        return $dto;
    }
}
