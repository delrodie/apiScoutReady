<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\DistrictOutput;
use App\Service\AllRepositories;
use App\Service\LogService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DistrictProvider implements ProviderInterface
{
    public function __construct(
        private AllRepositories $allRepositories,
        private RequestStack $requestStack,
        private LogService $logService
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Filtre par ID du district
        if (!empty($uriVariables['id'])){
            $district = $this->allRepositories->getOneDistrict($uriVariables['id'], 'ID');
            if (!$district) throw new NotFoundHttpException("Aucun district trouvé avec l'ID {$uriVariables['id']}.");

            $this->logService->log("Affichage du district '{$district->getNom()}'");
            return DistrictOutput::mapToOut($district);
        }

        // Filtre par ID de region ou tous les districts
        $request = $this->requestStack->getCurrentRequest();
        $regionId = $request?->query->get('region_id'); //dd($request);
        if ($regionId){
            $districts = $this->allRepositories->getDistrictsByRegionId($regionId);
        }else{
            $districts = $this->allRepositories->getAllDistrict();
        }

        $this->logService->log("Affichage de la liste des districts");
        return array_map([DistrictOutput::class, 'mapToOut'], $districts);
    }

}
