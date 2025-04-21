<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ScoutOutput;
use App\Entity\Scout;
use App\Service\AllRepositories;
use App\Service\Gestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScoutProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AllRepositories $allRepositories,
        private Gestion $_gestion,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Suppression du scout
        if ($operation->getMethod() === 'DELETE' && isset($uriVariables['id'])) {
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            if (!$scout) throw  new NotFoundHttpException("Impossible de supprimer le scout. L'ID {$uriVariables['id']} n'a pas été trouvé");
            $this->entityManager->remove($scout);
            $this->entityManager->flush();

            return null;
        }

        // Modification ou sauvegarde du scout
        if (!empty($uriVariables['id'])){
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            if (!$scout) throw new NotFoundHttpException("Echec de modification! Le scout avec l'ID {$uriVariables['id']} n'a pas été trouvé.");
        }else{
            $scout = new Scout();
        }

        $groupe = $this->allRepositories->getOneGroupe($data->groupe);
        if (!$groupe) throw new NotFoundHttpException("Le groupe associé n'a pas été trouvé!");

        try{
            $dateNaissance = $data->dateNaissance ? new \DateTime($data->dateNaissance) : null;
        } catch(\Exception $e){
            throw new \InvalidArgumentException("Le format de la date de naissance est invalide.");
        }

        $scout->setGroupe($groupe);
        $scout->setCode($this->_gestion->generateCode($data->statut));
        $scout->setMatricule($this->_gestion->validForm($data->matricule));
        $scout->setNom(strtoupper($this->_gestion->validForm($data->nom)));
        $scout->setPrenom(strtoupper($this->_gestion->validForm($data->prenom)));
        $scout->setDateNaissance($dateNaissance);
        $scout->setLieuNaissance($this->_gestion->validForm($data->lieuNaissance));
        $scout->setSexe(strtoupper($this->_gestion->validForm($data->sexe)));
        $scout->setTelephone($this->_gestion->validForm($data->telephone));
        $scout->setEmail($this->_gestion->validForm($data->email));
        $scout->setFonction($this->_gestion->validForm($data->fonction));
        $scout->setBranche($this->_gestion->validForm($data->branche));
        $scout->setStatut($this->_gestion->validForm($data->statut));
        $scout->setTelephoneParent($this->_gestion->validForm($data->telephoneParent));

        $this->entityManager->persist($scout);
        $this->entityManager->flush();

        return ScoutOutput::mapToOut($scout);
    }
}
