<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\UtilisationOutput;
use App\Entity\Utilisation;
use App\Service\AllRepositories;
use App\Service\Gestion;
use App\Service\Variables;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Variable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UtilisationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly AllRepositories $allRepositories,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request->getSchemeAndHttpHost();

        $uriId = $uriVariables['id'] ?? null;

        if ($operation->getMethod() === 'DELETE' && isset($uriId)){
            $utilisateur = $this->allRepositories->getUtilisateurDifferentFromStatut($uriId, Variables::UTILISATEUR_STATUT_APPROUVE);
            if (!$utilisateur) throw new NotFoundHttpException("L'utilisateur cherché ne peux être supprimé");
            $this->entityManager->remove($utilisateur);
            $this->entityManager->flush();

            return null;
        }

        // Mise a jour de l'entité Utilisation
        if (isset($uriId)){
            $utilisateur = $this->handleUpdate($uriId, $data);
            return UtilisationOutput::mapToOut($utilisateur, $baseUrl);
        }

        // Enregistrement de l'entité Utilisation
        $utilisateur = $this->handlePersistence($data);
        return UtilisationOutput::mapToOut($utilisateur, $baseUrl);
    }

    private function handlePersistence($data): Utilisation
    {
        $utilisateur = new Utilisation();

        if ($data->scout){
            $scout = $this->allRepositories->getOneScout(null, $data->scout);
            if (!$scout) throw new NotFoundHttpException("Echèc! Le scout concerné n'a pas pas été trouvé!");
            // Verification du scout
            $verifExist =  $this->allRepositories->getUtilisateurByScoutAndStatut($scout->getId());
            if ($verifExist) throw new BadRequestHttpException("Attention! Le Scout {$scout->getCode()} a déjà été affecté à un groupe cette année.");
            $utilisateur->setScout($scout);
        }

        if ($data->groupe){
            $groupe = $this->allRepositories->getOneGroupe((int) $data->groupe);
            if (!$groupe) throw new NotFoundHttpException("Echec! Le groupe concerné n'a pas été touvé");
            $utilisateur->setGroupe($groupe);
        }

        if ($data->demandeur){
            $scout = $this->allRepositories->getOneScout(null, $data->demandeur);
            if (!$scout) throw new NotFoundHttpException("Echec! Le demandeur n'a pas été trouvé!");
            $utilisateur->setDemandeur($data->demandeur);
        }

        $utilisateur->setStatut(Variables::UTILISATEUR_STATUT_ATTENTE);
        $utilisateur->setAnnee(Gestion::annee());

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return $utilisateur;
    }

    private function handleUpdate($uriId, $data)
    {
        //dd($data);
        $utilisateur = $this->allRepositories->getUtilisateurDifferentFromStatut($uriId, Variables::UTILISATEUR_STATUT_APPROUVE);
        if (!$utilisateur) throw new BadRequestHttpException("Echec, l'utilisateur selectionné ne peut être modifié");

        if ($data->statut){
            $statuts = [Variables::UTILISATEUR_STATUT_ATTENTE, Variables::UTILISATEUR_STATUT_APPROUVE, Variables::UTILISATEUR_STATUT_REJETE];
            if (!in_array($data->statut, $statuts)){
                throw new BadRequestHttpException("Echèc! Le statut envoyé n'existe pas. Veuillez conctacter les administrateurs");
            }
            $utilisateur->setStatut((int) $data->statut);
        }

        if ($data->groupe){
            $groupe = $this->allRepositories->getOneGroupe((int) $data->groupe);
            if (!$groupe) throw new BadRequestHttpException("Echèc!, Le groupe selectioné n'a pas été trouvé");
            $utilisateur->setGroupe($groupe);
        }

        if ($data->approbateur){
            $scout = $this->allRepositories->getOneScout(null, $data->approbateur);
            if (!$scout) throw new BadRequestHttpException("Echec! L'approbateur n'existe pas dans le système");
            $utilisateur->setApprobateur($data->approbateur);
        }

        $this->entityManager->flush();

        return $utilisateur;
    }
}
