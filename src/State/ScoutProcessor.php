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
//        dd($operation);

        // Suppression du scout
        if ($operation->getMethod() === 'DELETE' && isset($uriVariables['id'])) {
            $scout = $this->allRepositories->getOneScout($uriVariables['id']);
            if (!$scout) throw  new NotFoundHttpException("Impossible de supprimer le scout. L'ID {$uriVariables['id']} n'a pas été trouvé");

            $utilisation = $this->allRepositories->getUtilisateurByScout($scout->getId());
            if ($utilisation){
                $this->entityManager->remove($utilisation);
            }
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
            // Verification du numéro de telephone
            if ($this->existenceTelephone($data->telephone)){
                throw new \Exception("Echèc! Le numéro de telephone '{$data->telephone}' appartient déjà à un autre scout.");
            }

            $code = $this->_gestion->generateCode($data->statut); // Generation du code
            $scout->setCode($code);
        }
        dd($data);
        $groupe = $this->allRepositories->getOneGroupe($data->groupe);
        if (!$groupe) throw new NotFoundHttpException("Le groupe associé n'a pas été trouvé!");

        try{
            $dateNaissance = $data->scdateNaissance ? new \DateTime($data->dateNaissance) : null;
        } catch(\Exception $e){
            throw new \InvalidArgumentException("Le format de la date de naissance est invalide.");
        }

        dd($data);

        $scout->setGroupe($groupe);
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
        $scout->setTelephoneParent(filter_var($data->telephoneParent, FILTER_VALIDATE_BOOLEAN));
        $scout->setQrcode($this->qrCode->qrCodeGenerator($code));

        if (is_string($data->photo) && $data->photo !== '') {
            if ($scout->getPhoto()) {
                $this->gestionMedia->removeUpload($scout->getPhoto(), 'profile');
            }

            $scout->setPhoto($data->photo);
        }

        $utilisation = $this->_gestion->saveUtilisation(
            $scout,
            [
                'groupe' => $groupe,
                'demandeur' => $data->demandeur ?? $scout->getCode(),
            ]
        );

        if (!$utilisation) throw new \Exception("Echèc! le scout {$scout->getCode()} a déjà été enregistré pour cette année.");

        $this->entityManager->persist($scout);
        $this->entityManager->persist($utilisation);
        $this->entityManager->flush();

        return ScoutOutput::mapToOut($scout, $baseUrl);
    }

    private function existenceTelephone(?string $telephone): bool
    {
        $verif = $this->allRepositories->getOneScoutByTelephone($telephone);
        if ($verif) return true;

        return false;
    }

  
}
