<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\GroupeOutput;
use App\Entity\Groupe;
use App\Service\AllRepositories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AllRepositories $allRepositories
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Suppression du groupe
        if ($operation->getMethod() === 'DELETE' && $uriVariables['id'] !== null) {
            $groupe = $this->allRepositories->getOneGroupe($uriVariables['id']);
            if(!$groupe) throw  new NotFoundHttpException("Impossible de supprimer ce groupe concerné par l'ID: {$uriVariables['id']} car il n'a pas été trouvé");
            $this->entityManager->remove($groupe);
            $this->entityManager->flush();

            return null;
        }

        // Modification ou enregistrement
        if (!empty($uriVariables['id'])){
            $groupe = $this->allRepositories->getOneGroupe($uriVariables['id']);
            if (!$groupe) throw  new NotFoundHttpException("Imossible de modifier le groupe concerné par l'ID {$uriVariables['id']} car il n'a pas été trouvé");
        }else{
            $groupe = new Groupe();
        }

        $district = $this->allRepositories->getOneDistrict($data->district);
        if (!$district) throw new NotFoundHttpException("Echec! Le district associé n'a pas été trouvé");

        $groupe->setParoisse($data->paroisse);
        $groupe->setDistrict($district);
        $this->entityManager->persist($groupe);
        $this->entityManager->flush();

        return GroupeOutput::mapToOut($groupe);
    }
}
