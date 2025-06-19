<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\ScoutInput;
use App\DTO\ScoutOutput;
use App\Entity\Scout;
use App\Service\AllRepositories;
use App\Service\Gestion;
use App\Service\GestionMedia;
use App\Service\GestionQrCode;
use App\Service\LogService;
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
        private GestionMedia $gestionMedia,
        private LogService $logService,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof ScoutInput){
            $this->logService->errorLog("ScoutProcessor : $data n'est pas une instance de ScoutInput comme attendu. Type réçu: ".gettype($data));
            if (is_array($data)){
                $this->logService->errorLog("Contenu de $data (array): $data");
            }
            throw new BadRequestHttpException('Données de requête invalides');
        }
        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request->getSchemeAndHttpHost();
        $this->logService->log("ScoutProcessor: Début du traitement de la requête POST.");
        $this->logService->log("ScoutProcessor: Données du DTO ScoutInput: ", (array)$data);

        if ($operation->getMethod() === 'DELETE' && isset($uriVariables['id'])) {
            return $this->deleteScout($uriVariables['id']);
        }

        $scout = $this->handleScoutPersistence($data, $uriVariables);

        $this->logService->log("ScoutProcessor: Fin du traitement.");
        return ScoutOutput::mapToOut($scout, $baseUrl);
    }

    private function deleteScout(int|string $id): ?ScoutOutput
    {
        $scout = $this->allRepositories->getOneScout($id);
        if (!$scout) {
            $this->logService->log("Impossible de supprimer le scout. L'ID {$id} n'a pas été trouvé");
            throw  new NotFoundHttpException("Impossible de supprimer le scout. L'ID {$id} n'a pas été trouvé");
        }

        $utilisation = $this->allRepositories->getUtilisateurByScout($scout->getId());
        if ($utilisation){
            $this->entityManager->remove($utilisation);
        }
        $this->entityManager->remove($scout);
        $this->entityManager->flush();

        $this->logService->log("L'utilisateur a supprimé un scout");

        return null;
    }

    private function handleScoutPersistence(mixed $data, array $uriVariables): Scout
    {
        $scout = null;
        $this->logService->log('Accès au ScoutProcessor ');

        if (!empty($uriVariables['id'])){
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            $this->logService->log("Tentative de modifiaction du scout $scout.");
            if (!$scout) throw new NotFoundHttpException("Echec de modification! Le scout avec l'ID {$uriVariables['id']} n'a pas été trouvé.");
        }else{
            $this->checkIfTelephoneExists($data->telephone);
            $scout = new Scout();
            $code = $this->_gestion->generateCode($data->statut);
            $scout->setCode($code);
            $scout->setQrcode($this->qrCode->qrCodeGenerator($code));
        }

        $groupe = $this->allRepositories->getOneGroupe((int) $data->groupe);
        if (!$groupe) {
            $this->logService->log("Le groupe {$data->groupe} n'a pas été trouvé!");
            throw new NotFoundHttpException("Le groupe associé n'a pas été trouvé!");
        }

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

        $this->logService->log("Le scout '{$scout->getCode()} a été enregistré avec succès");

        return $scout;
    }
    
    private function mapDataToScout(Scout $scout, mixed $data, $groupe): Scout
    {
        $this->logService->log("ScoutProcessor: mapDataToScout - Début du mappage des données");
        $scout->setGroupe($groupe);
        $scout->setMatricule($this->_gestion->validForm($data->matricule));
        $scout->setNom(strtoupper($this->_gestion->validForm($data->nom)));
        $scout->setPrenom(strtoupper($this->_gestion->validForm($data->prenom)));
//        $scout->setDateNaissance($data->dateNaissance);
        $scout->setDateNaissance($this->parseDate($data->dateNaissance));
        $scout->setLieuNaissance($this->_gestion->validForm($data->lieuNaissance));
        $scout->setSexe(strtoupper($this->_gestion->validForm($data->sexe)));
        $scout->setTelephone($this->_gestion->validForm($data->telephone));
        $scout->setEmail($this->_gestion->validForm($data->email));
        $scout->setFonction($this->_gestion->validForm($data->fonction));
        $scout->setBranche($this->_gestion->validForm($data->branche));
        $scout->setStatut($this->_gestion->validForm($data->statut));
        $scout->setTelephoneParent(filter_var($data->telephoneParent, FILTER_VALIDATE_BOOLEAN));

        // Gestion d l'upload du fichier
        if ($data->photo instanceof UploadedFile) {
            try{
                $fileName = $this->gestionMedia->upload($data->photo, 'profile');
                if ($scout->getPhoto()){
                    $this->gestionMedia->removeUpload($scout->getPhoto(), 'profile');
                }
                $scout->setPhoto($fileName);
                $this->logService->log("ScoutProcessor: photo uploadée et définie via UploadFile (DTO): $fileName");
            } catch(\Exception $e){
                $this->logService->errorLog("ScoutProcessor: Erreur lors de l'upload de la photo: {$e->getMessage()}");
            }
        }elseif  (is_string($data->photo) && $data->photo !== '') {
            if ($scout->getPhoto()) {
                $this->gestionMedia->removeUpload($scout->getPhoto(), 'profile');
            }
            $scout->setPhoto($data->photo);
            $this->logService->log("ScoutProcessor: photo définie par une chaîne exitante (DTO): {$data->photo}");
        }else{
            $this->logService->log("ScoutProcessor: Pas de fichier photo uploadé via DTO et pas de chaîne photo existante fournie.");
        }

        $this->logService->log("ScoutProcessor: mapDataToScout - Fin du mappage");
        return $scout;
    }

    private function parseDate(null|string|\DateTimeInterface $date): ?\DateTimeInterface
    {
        if ($date instanceof \DateTimeInterface) {
            return $date;
        }

        if (!$date || trim($date) === '') {
            error_log('parseDate(): date vide ou null');
            return null;
        }

        try {
            return new \DateTime($date);
        } catch (\Exception $e) {
            error_log("parseDate() ERREUR: " . $e->getMessage());
            throw new BadRequestHttpException("Format de date invalide: " . $e->getMessage());
        }
    }


    private function checkIfTelephoneExists(?string $telephone): void
    {
        if (!$telephone) return;
        if($this->allRepositories->getOneScoutByTelephone($telephone)){
            $this->logService->log("Echèc! le numéro de telephone '{$telephone}' appartient déjà à un autre scout");
            throw new BadRequestHttpException("Echèc! le numéro de telephone '{$telephone}' appartient déjà à un autre scout");
        }
    }

  
}
