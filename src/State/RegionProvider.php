<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\RegionOutput;
use App\Service\AllRepositories;
use App\Service\LogService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegionProvider implements ProviderInterface
{
    public function __construct(
        private AllRepositories $allRepositories,
        private LogService $logService,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!empty($uriVariables['id'])){
            $region = $this->allRepositories->getOneRegion($uriVariables['id']);
            if (!$region) throw  new NotFoundHttpException("Aucune region trouvée avec l'identifiant {$uriVariables['id']}.");

            $this->logService->log("Affichage de la region: '{$region->getNom()}'");
            return RegionOutput::mapToOut($region);
        }

        $regions = $this->allRepositories->getAllRegion();
        $this->logService->log("Affichage de la liste des régions");

        return array_map([RegionOutput::class, 'mapToOut'], $regions);
    }

}
