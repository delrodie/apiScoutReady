<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\ScoutOutput;
use App\Service\AllRepositories;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScoutProvider implements ProviderInterface
{
    public function __construct(
        private AllRepositories $allRepositories,
        private RequestStack $requestStack
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (isset($uriVariables['id'])){
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            if (!$scout) throw new NotFoundHttpException("Aucun scout n'a été trouvé avec l'ID {$uriVariables['id']}");

            return ScoutOutput::mapToOut($scout);
        }

        $request = $this->requestStack->getCurrentRequest();
        $code = $request?->query->get('code');
        $matricule = $request?->query->get('matricule');
        $groupeId = $request?->query->get('groupe_id');
        $districtId = $request?->query->get('district_id');
        $regionId = $request?->query->get('region_id');
        $asnId = $request?->query->get('asn_id');

        if ($code){
            $scout = $this->allRepositories->getOneScout(null, $code);
            if (!$scout) throw new NotFoundHttpException("Oups!! Le scout ayant le code {$code} n'a pas été trouvé");
            return ScoutOutput::mapToOut($scout);
        }

        if ($matricule){
            $scout = $this->allRepositories->getOneScout(null, null, $matricule);
            if (!$scout) throw new NotFoundHttpException("Oups!! Le scout ayant le matricule {$matricule} n'a pas été trouvé");
            return ScoutOutput::mapToOut($scout);
        }

        $scouts = match (true){
            !is_null($groupeId) => $this->allRepositories->getAllScoutOrByQuery($groupeId),
            !is_null($districtId) => $this->allRepositories->getAllScoutOrByQuery(null, $districtId),
            !is_null($regionId) => $this->allRepositories->getAllScoutOrByQuery(null, null, $regionId),
            !is_null($asnId) => $this->allRepositories->getAllScoutOrByQuery(null, null, null, $asnId),
            default => $this->allRepositories->getAllScoutOrByQuery(),
        };

        return array_map([ScoutOutput::class, 'mapToOut'], $scouts);
    }
}
