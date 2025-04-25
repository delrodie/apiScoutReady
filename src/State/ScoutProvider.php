<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\ScoutOutput;
use App\Service\AllRepositories;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ScoutProvider implements ProviderInterface
{
    public function __construct(
        private AllRepositories $allRepositories,
        private RequestStack $requestStack,
        private ScoutOutput $scoutOutput,
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        if (isset($uriVariables['id'])){
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            if (!$scout) throw new NotFoundHttpException("Aucun scout n'a été trouvé avec l'ID {$uriVariables['id']}");

            return ScoutOutput::mapToOut($scout, $baseUrl);
        }

        $request = $this->requestStack->getCurrentRequest();

        if ($request?->query->count() > 0){
            $code = $request?->query->get('code');
            $matricule = $request?->query->get('matricule');
            $telephone = $request?->query->get('telephone');
            $groupeId = $request?->query->get('groupe');
            $districtId = $request?->query->get('district');
            $regionId = $request?->query->get('region');
            $asnId = $request?->query->get('asn');

            if ($code){
                $scout = $this->allRepositories->getOneScout(null, $code);
                if (!$scout) throw new NotFoundHttpException("Oups!! Le scout ayant le code {$code} n'a pas été trouvé");
                return ScoutOutput::mapToOut($scout, $baseUrl);
            }

            if ($matricule){
                $scout = $this->allRepositories->getOneScout(null, null, $matricule);
                if (!$scout) throw new NotFoundHttpException("Oups!! Le scout ayant le matricule {$matricule} n'a pas été trouvé");
                return ScoutOutput::mapToOut($scout, null);
            }

            $scouts = match (true){
                !is_null($groupeId) => $this->allRepositories->getAllScoutOrByQuery($groupeId, AllRepositories::GROUPE),
                !is_null($districtId) => $this->allRepositories->getAllScoutOrByQuery($districtId, AllRepositories::DISTRICT),
                !is_null($regionId) => $this->allRepositories->getAllScoutOrByQuery($regionId, AllRepositories::REGION),
                !is_null($asnId) => $this->allRepositories->getAllScoutOrByQuery($asnId, AllRepositories::ASN),
                !is_null($telephone) => $this->allRepositories->getAllScoutOrByQuery($telephone, AllRepositories::TELEPHONE),
                default => throw new \Exception("Vos paramètres de requêtes n'ont pas été définis. Veuillez contacter les administrateurs!"),
            };

            return array_map(fn($scout) => ScoutOutput::mapToOut($scout, $baseUrl), $scouts);
        }


        $scouts = $this->allRepositories->getAllScoutOrByQuery();

        return array_map(fn($scout) => ScoutOutput::mapToOut($scout, $baseUrl), $scouts);


//        return array_map([ScoutOutput::class, 'mapToOut'], $scouts);
    }
}
