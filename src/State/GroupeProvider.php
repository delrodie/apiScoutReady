<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\GroupeOutput;
use App\Service\AllRepositories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupeProvider implements ProviderInterface
{
    public function __construct(private AllRepositories $allRepositories, private RequestStack $requestStack)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!empty($uriVariables['id'])){
            $groupe = $this->allRepositories->getOneGroupe($uriVariables['id']);
            if (!$groupe) throw new NotFoundHttpException("Le groupe n'a pas été trouvé");

            return GroupeOutput::mapToOut($groupe);
        }

        $request = $this->requestStack->getCurrentRequest();
        $districtId = $request?->query->get('district_id');
        $regionId = $request?->query->get('region_id');

        $groupes = match (true){
            !is_null($districtId) => $this->allRepositories->getAllGroupeOrByQuery($districtId),
            !is_null($regionId) => $this->allRepositories->getAllGroupeOrByQuery(null, $regionId),
            default => $this->allRepositories->getAllGroupeOrByQuery(),
        };

        return array_map([GroupeOutput::class, 'mapToOut'], $groupes);
    }


}
