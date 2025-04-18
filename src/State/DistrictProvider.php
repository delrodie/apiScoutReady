<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\DistrictOutput;
use App\Service\AllRepositories;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DistrictProvider implements ProviderInterface
{
    public function __construct(
        private AllRepositories $allRepositories,
        private RequestStack $requestStack,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Filtre par ID du district
        if (!empty($uriVariables['id'])){
            $district = $this->allRepositories->getOneDistrict($uriVariables['id']);
            if (!$district) throw new NotFoundHttpException("Aucun district trouvé avec l'ID {$uriVariables['id']}.");

            return $this->mapToOut($district);
        }

        // Filtre par ID de region ou tous les districts
        $request = $this->requestStack->getCurrentRequest();
        $regionId = $request?->query->get('region_id'); //dd($request);
        if ($regionId){
            $districts = $this->allRepositories->getDistrictsByRegionId($regionId);
        }else{
            $districts = $this->allRepositories->getAllDistrict();
        }

        return array_map([$this, 'mapToOut'], $districts);
    }

    private function mapToOut($district)
    {
        $dto = new DistrictOutput();
        $dto->id = $district->getId();
        $dto->nom = $district->getNom();
        $dto->region = $district->getRegion();

        return $dto;
    }
}
