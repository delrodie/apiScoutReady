<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\ComplementaireOutput;
use App\Service\AllRepositories;
use App\Service\Gestion;
use App\Service\LogService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ComplementaireProvider implements ProviderInterface
{
    public function __construct(
        private AllRepositories $allRepositories,
        private LogService $logService,
        private RequestStack $requestStack,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if(!empty($uriVariables['id'])){
            $complementaire = $this->allRepositories->getOneComplementaire($uriVariables['id']);
            return $this->renderComplementaire($complementaire);
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request?->query->count() > 0){
            $scout = $request?->query->get('scout');

            if (!$scout){
                $message = "Le scout n'a pas été trouvée ";
                $this->logService->errorLog($message);
                throw new NotFoundHttpException($message);
            }

            $complementaire = $this->allRepositories->getOneComplementaire(null, $scout);
            return $this->renderComplementaire($complementaire);
        }

        $complementaires = $this->allRepositories->getAllComplementaire();

        $this->logService->log("L'utilisateur a consulté la liste des informations complementiares");
        return array_map([ComplementaireOutput::class, 'mapToOut'], $complementaires);
    }

    protected function renderComplementaire($complementaire)
    {
        if (!$complementaire) {
            $message = "Aucune information complémentaire n'a été trouvée avec la variable soumise";
            $this->logService->errorLog($message);
            throw new NotFoundHttpException($message);
        }

        $this->logService->log("L'utilisateur a consulté l'information complémentaire ayant l'identifiant {$complementaire->getId()}");
        return ComplementaireOutput::mapToOut($complementaire);
    }
}
