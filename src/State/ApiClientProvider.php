<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\ApiClientOutput;
use App\Service\AllRepositories;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiClientProvider implements ProviderInterface
{
    public function __construct(
        private readonly AllRepositories $allRepositories,
        private RequestStack $requestStack
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!empty($uriVariables['id'])){
            return $this->searchClient($uriVariables['id'], 'ID');
        }


        $request = $this->requestStack->getCurrentRequest();
        if ($request?->query->count() > 0){
            $name = $request?->query->get('name');
            $key = $request?->query->get('apiKey');
            
            if ($name){
                return $this->searchClient($name, 'NAME');
            }

            if($key){
                return $this->searchClient($key, 'KEY');
            }
            
            throw new \InvalidArgumentException("Oups! Votre paramètre de requête n'est pas définie. Veuillez contacter les administrateurs");
        }

        $apiKeys = $this->allRepositories->getAllClients();

        return array_map([ApiClientOutput::class, 'mapToOUt'], $apiKeys);
    }

    private function searchClient($value, ?string $type): ApiClientOutput
    {
        $client = $this->allRepositories->getOneClient($value, $type);
        if(!$client){
            throw new NotFoundHttpException("La clé recherchée n'a pas été trouvée.");
        }

        return ApiClientOutput::MapToOut($client);
    }
}