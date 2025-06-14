<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ScoutOutput;
use App\Entity\Scout;
use App\Service\AllRepositories;
use App\Service\Gestion;
use App\Service\GestionMedia;
use App\Service\GestionQrCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScoutProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AllRepositories $allRepositories,
        private Gestion $_gestion,
        private GestionQrCode $qrCode,
        private RequestStack $requestStack,
        private GestionMedia $gestionMedia
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request->getSchemeAndHttpHost();

        if ($operation->getMethod() === 'DELETE' && isset($uriVariables['id'])) {
            return $this->deleteScout($uriVariables['id']);
        }

        $scout = $this->handleScoutPersistence($data, $uriVariables);

        return ScoutOutput::mapToOut($scout, $baseUrl);
    }

    private function deleteScout(int|string $id): ?ScoutOutput
    {
        $scout = $this->allRepositories->getOneScout($id);
        if (!$scout) throw  new NotFoundHttpException("Impossible de supprimer le scout. L'ID {$id} n'a pas été trouvé");

        $utilisation = $this->allRepositories->getUtilisateurByScout($scout->getId());
        if ($utilisation){
            $this->entityManager->remove($utilisation);
        }
        $this->entityManager->remove($scout);
        $this->entityManager->flush();

        return null;
    }

    private function handleScoutPersistence(mixed $data, array $uriVariables): Scout
    {
        $scout = null;

        if (!empty($uriVariables['id'])){
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            if (!$scout) throw new NotFoundHttpException("Echec de modification! Le scout avec l'ID {$uriVariables['id']} n'a pas été trouvé.");
        }else{
            $this->checkIfTelephoneExists($data->telephone);
            $scout = new Scout();
            $code = $this->_gestion->generateCode($data->statut);
            $scout->setCode($code);
            $scout->setQrcode($this->qrCode->qrCodeGenerator($code));
        }

        $groupe = $this->allRepositories->getOneGroupe((int) $data->groupe);
        if (!$groupe) throw new NotFoundHttpException("Le groupe associé n'a pas été trouvé!");

        $scout = $this->mapDataToScout($scout, $data, $groupe);

        $utilisation = $this->_gestion->saveUtilisation(
            $scout,
            [
                'groupe' => $groupe,
                'demandeur' => $data->demandeur ?? $scout->getCode(),
            ]
        );

        if (!$utilisation) throw new BadRequestHttpException("Echèc! le scout {$scout->getCode()} a déjà été enregistré pour cette année.");

        $this->entityManager->persist($scout);
        $this->entityManager->persist($utilisation);
        $this->entityManager->flush();

        return $scout;
    }
    
    private function mapDataToScout(Scout $scout, mixed $data, $groupe): Scout
    {
        $scout->setGroupe($groupe);
        $scout->setMatricule($this->_gestion->validForm($data->matricule));
        $scout->setNom(strtoupper($this->_gestion->validForm($data->nom)));
        $scout->setPrenom(strtoupper($this->_gestion->validForm($data->prenom)));
        $scout->setDateNaissance($this->parseDate($data->dateNaissance));
        $scout->setLieuNaissance($this->_gestion->validForm($data->lieuNaissance));
        $scout->setSexe(strtoupper($this->_gestion->validForm($data->sexe)));
        $scout->setTelephone($this->_gestion->validForm($data->telephone));
        $scout->setEmail($this->_gestion->validForm($data->email));
        $scout->setFonction($this->_gestion->validForm($data->fonction));
        $scout->setBranche($this->_gestion->validForm($data->branche));
        $scout->setStatut($this->_gestion->validForm($data->statut));
        $scout->setTelephoneParent(filter_var($data->telephoneParent, FILTER_VALIDATE_BOOLEAN));

        if (is_string($data->photo) && $data->photo !== '') {
            if ($scout->getPhoto()) {
                $this->gestionMedia->removeUpload($scout->getPhoto(), 'profile');
            }
            $scout->setPhoto($data->photo);
        }

        return $scout;
    }

    private function parseDate(?string $date): ?\DateTime
    {
        if (!$date) return null;
        try {
            return new \DateTime($date);
        } catch(\Exception $e) {
            throw new BadRequestHttpException("Le format de la date de naissance est invalide: {$e}");
        }
    }
    
    private function checkIfTelephoneExists(?string $telephone): void
    {
        if (!$telephone) return;
        if($this->allRepositories->getOneScoutByTelephone($telephone)){
            throw new BadRequestHttpException("Echèc! le numéro de telephone '{$telephone}' appartient déjà à un autre scout");
        }
    }

  
}
