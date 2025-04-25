<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ApiClientOutput;
use App\Entity\ApiClient;
use App\Service\AllRepositories;
use App\Service\Gestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiClientProcessor implements ProcessorInterface
{
    public function __construct(private readonly AllRepositories $allRepositories, private readonly EntityManagerInterface $entityManager, private readonly Gestion $gestion)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation->getMethod() === 'DELETE' && isset($uriVariables['id'])) {
            $apiClient = $this->allRepositories->getOneClient($uriVariables['id'], 'ID');
            if (!$apiClient) throw new \Exception("Impossible de supprimer cette clé car elle est inrouvable");
            
            $this->entityManager->remove($apiClient);
            $this->entityManager->flush();
            
            return null;
        }

        if (isset($uriVariables['id'])){
            $apiClient = $this->allRepositories->getOneClient($uriVariables['id'], 'ID');
            if (!$apiClient) throw new NotFoundHttpException("Impossible de modifier car la clé n'a pas été trouvée");
        }else{
            $apiClient = new ApiClient();
        }

        // VERIFICATION DU ROLE
        $rolesValids = ['ROLE_API', 'ROLE_MOBILE', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
        $requestRoles = $data->roles ?? [];

        foreach ($requestRoles as $role){
            if (!in_array($role, $rolesValids, true)){
                throw new \InvalidArgumentException("Le rôle '{$role}' n'est pas autorisé");
            }
        }

        $apiClient->setName(strtoupper($this->gestion->validForm($data->name)));
        $apiClient->setRoles($requestRoles);

        $this->entityManager->persist($apiClient);
        $this->entityManager->flush();

        return ApiClientOutput::MapToOut($apiClient);
    }
}