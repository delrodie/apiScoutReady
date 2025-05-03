<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\UtilisationOutput;
use App\Entity\Utilisation;
use App\Repository\UtilisationRepository;
use App\Service\AllRepositories;
use App\Service\Variables;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UtilisationProvider implements ProviderInterface
{
    public function __construct(private readonly UtilisationRepository $utilisationRepository, private readonly RequestStack $requestStack, private readonly AllRepositories $allRepositories)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request->getSchemeAndHttpHost();
        if (!empty($uriVariables['id'])){
            $utilisation = $this->utilisationRepository->findOneBy(['id' => $uriVariables['id']]);
            if (!$utilisation) throw new NotFoundHttpException("Ce compte utilisateur n'a pas été trouvé");

            return UtilisationOutput::mapToOut($utilisation, $baseUrl);
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request?->query->count() > 0) {
            $groupeId = $request?->query->get('groupe');
            $telephone = $request?->query->get('telephone');
            $code = $request?->query->get('code');
            $matricule = $request?->query->get('matricule');
            $districtId = $request?->query->get('district');
            $regionId = $request?->query->get('region');
            $asnId = $request?->query->get('asn');
            $page = $request?->query->get('page');

            if ($code){
                $utilisateur = $this->allRepositories->getUtilisateurByQuery($code, Variables::CODE);
                if (!$utilisateur) throw new NotFoundHttpException("Oups! Le scout associé à ce code {$code} n'a pas actualisé ses informations cette année.");
                return UtilisationOutput::mapToOut($utilisateur, $baseUrl);
            }

            if ($matricule){
                $utilisateur = $this->allRepositories->getUtilisateurByQuery($matricule, Variables::MATRICULE);
                if (!$utilisateur) throw new NotFoundHttpException("Oups! le scout asscocié à ce matricule {$matricule} n'a pas actualisé ses informations cette année");
                return UtilisationOutput::mapToOut($utilisateur, $baseUrl);
            }

            $utilisateurs = match(true){
                !is_null($telephone) => $this->allRepositories->getUtilisateurByQuery($telephone, Variables::TELEPHONE),
                !is_null($groupeId) => $this->allRepositories->getUtilisateurByQuery($groupeId, Variables::GROUPE),
                !is_null($districtId) => $this->allRepositories->getUtilisateurByQuery($districtId, Variables::DISTRICT),
                !is_null($regionId) => $this->allRepositories->getUtilisateurByQuery($regionId, Variables::REGION),
                !is_null($asnId) => $this->allRepositories->getUtilisateurByQuery($asnId, Variables::ASN),
                !is_null($page) => $this->allRepositories->getUtilisateurByQuery($page, Variables::PAGE),
                default => throw new BadRequestHttpException("Vos paramètres de requêtes n'ont pas été définis. Veuillez donc contacter les adminsitrateurs")
            };

            return array_map(fn($utilisateur) => UtilisationOutput::mapToOut($utilisateur, $baseUrl), $utilisateurs);
        }

        $utilisateurs = $this->allRepositories->getUtilisateurByQuery(1, Variables::PAGE);
        return array_map(fn($utilisateur) => UtilisationOutput::mapToOut($utilisateur, $baseUrl), $utilisateurs);
    }
}
