<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ComplementaireOutput;
use App\Entity\Complementaire;
use App\Service\AllRepositories;
use App\Service\Gestion;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ComplementaireProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AllRepositories $allRepositories,
        private LogService $logService,
        private Gestion $gestion,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $id = $uriVariables['id'] ?? null;

        // Suppression de l'entité
        if ($operation->getMethod() === 'DELETE' && isset($id)){
            $complementaire = $this->allRepositories->getOneComplementaire($id);
            $this->complementaireNotFound($complementaire);

            $this->entityManager->remove($complementaire);
            $this->entityManager->flush();
        }

        // Modification ou creation
        if ($id){
            $complementaire = $this->allRepositories->getOneComplementaire($id);
            $this->complementaireNotFound($complementaire);
        }else{
            $complementaire = new Complementaire();
        }

        // Le scout associé
        $scout = $this->allRepositories->getOneScout($data->scout);
        if (!$scout) throw new NotFoundHttpException("Le socut associé n'a pas été trouvé");
        
        $complementaire->setScout($scout);
        $complementaire->setFormation((bool) $this->gestion->validForm($data->formation));
        $complementaire->setBrancheOrigine($this->gestion->validForm($data->brancheOrigine));
        $complementaire->setBaseNiveau1( $this->gestion->validForm($data->baseNiveau1));
        $complementaire->setAnneeBaseNiveau1((int) $this->gestion->validForm($data->anneeBaseNiveau1));
        $complementaire->setBaseNiveau2($this->gestion->validForm($data->baseNiveau2));
        $complementaire->setAnneeBaseNiveau2((int) $this->gestion->validForm($data->anneeBaseNiveau2));
        $complementaire->setAvanceNiveau1($this->gestion->validForm($data->avanceNiveau1));
        $complementaire->setAnneeAvanceNiveau1((int) $this->gestion->validForm($data->anneeAvanceNiveau1));
        $complementaire->setAvanceNiveau2($this->gestion->validForm($data->avanceNiveau2));
        $complementaire->setAnneeAvanceNiveau2((int) $this->gestion->validForm($data->anneeAvanceNiveau2));
        $complementaire->setAvanceNiveau3($this->gestion->validForm($data->avanceNiveau3));
        $complementaire->setAnneeAvanceNiveau3((int) $this->gestion->validForm($data->anneeAvanceNiveau3));
        $complementaire->setAvanceNiveau4($this->gestion->validForm($data->avanceNiveau4));
        $complementaire->setAnneeAvanceNiveau4((int) $this->gestion->validForm($data->anneeAvanceNiveau4));

        $this->entityManager->persist($complementaire);
        $this->entityManager->flush();

        return ComplementaireOutput::mapToOut($complementaire);
    }

    protected function complementaireNotFound($complementaire)
    {
        if (!$complementaire){
            $message = "Aucune information complementaire n'a été trouvée";
            $this->logService->errorLog($message);
            return throw new NotFoundHttpException($message);
        }
    }
}
