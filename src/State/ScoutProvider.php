<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\ScoutOutput;
use App\Service\AllRepositories;
use App\Service\LogService;
use App\Service\Variables;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ScoutProvider implements ProviderInterface
{
    public function __construct(
        private AllRepositories $allRepositories,
        private RequestStack $requestStack,
        private ScoutOutput $scoutOutput,
        private UrlGeneratorInterface $urlGenerator,
        private LogService $logService
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        if (isset($uriVariables['id'])){
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            if (!$scout) {
                $this->logService->log("Aucun scout n'a été trouvé avec l'ID {$uriVariables['id']}");
                throw new NotFoundHttpException("Aucun scout n'a été trouvé avec l'ID {$uriVariables['id']}");
            }

            $this->logService->log("L'utilisateur a consulté le scout {$scout->getCode()}");
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
            $page = $request?->query->get('page');
            $branche = $request?->query->get('branche');

            if ($code){
                $scout = $this->allRepositories->getOneScout(null, $code);
                if (!$scout) {
                    $this->logService->log("Oups!! Le scout ayant le code {$code} n'a pas été trouvé");
                    throw new NotFoundHttpException("Oups!! Le scout ayant le code {$code} n'a pas été trouvé");
                }

                $this->logService->log("L'utilisateur a consulté le scout ayant le code {$code}");
                return ScoutOutput::mapToOut($scout, $baseUrl);
            }

            if ($matricule){
                $scout = $this->allRepositories->getOneScout(null, null, $matricule);
                if (!$scout) {
                    $this->logService->log("Oups!! Le scout ayant le matricule {$matricule} n'a pas été trouvé");
                    throw new NotFoundHttpException("Oups!! Le scout ayant le matricule {$matricule} n'a pas été trouvé");
                }

                $this->logService->log("L'utilisateur a consulté le scout ayant le code {$matricule}");
                return ScoutOutput::mapToOut($scout, null);
            }

            $scouts = match (true){
                !is_null($groupeId) => $this->allRepositories->getAllScoutOrByQuery($groupeId, Variables::GROUPE),
                !is_null($districtId) => $this->allRepositories->getAllScoutOrByQuery($districtId, Variables::DISTRICT),
                !is_null($regionId) => $this->allRepositories->getAllScoutOrByQuery($regionId, Variables::REGION),
                !is_null($asnId) => $this->allRepositories->getAllScoutOrByQuery($asnId, Variables::ASN),
                !is_null($telephone) => $this->allRepositories->getAllScoutOrByQuery($telephone, Variables::TELEPHONE),
                !is_null($branche) => $this->allRepositories->getAllScoutOrByQuery($branche, Variables::BRANCHE),
                !is_null($page) => $this->allRepositories->getAllScoutOrByQuery(),
                default => throw new \Exception("Vos paramètres de requêtes n'ont pas été définis. Veuillez contacter les administrateurs!"),
            };

            $this->logService->log("L'utilisateur a consulté la liste des scouts");
            return array_map(fn($scout) => ScoutOutput::mapToOut($scout, $baseUrl), $scouts);
        }


        $scouts = $this->allRepositories->getAllScoutOrByQuery();

        $this->logService->log("L'utilisateur a consulté la liste des scouts: {$request}");

        return array_map(fn($scout) => ScoutOutput::mapToOut($scout, $baseUrl), $scouts);


//        return array_map([ScoutOutput::class, 'mapToOut'], $scouts);
    }
}
