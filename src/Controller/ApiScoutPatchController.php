<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ScoutOutput;
use App\Entity\Scout;
use App\Service\AllRepositories;
use App\Service\Gestion;
use App\Service\GestionMedia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiScoutPatchController extends AbstractController
{
    public function __construct(private readonly AllRepositories $allRepositories, private readonly Gestion $gestion, private readonly GestionMedia $gestionMedia, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(
        Request $request,
        $id,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse|ScoutOutput
    {

       $reqScout = $this->allRepositories->getOneScout((int) $id);
       if (!$reqScout){
           return new JsonResponse(['error' => "Scout non trouvé"], 404);
       }

       $scout = $this->assignRequestData($request, $reqScout);

        $this->processUploadedPhoto($request, $scout); //dd($scout);

        $this->entityManager->flush();

        return new JsonResponse([
            'statut' => "Mise à jour effectuée avec succès!"
        ], 200);
    }

    private function assignRequestData($request, object $scout): object
    {
        if ($matricule = $request->get('matricule')){
            $scout->setMatricule(strtoupper($this->gestion->validForm($matricule)));
        }

        if ($nom = $request->get('nom')){
            $scout->setNom(strtoupper($this->gestion->validForm($nom)));
        }

        if ($prenom = $request->get('prenom')){
            $scout->setPrenom(strtoupper($this->gestion->validForm($prenom)));
        }

        if ($sexe = $request->get('sexe')){
            $scout->setSexe(strtoupper($this->gestion->validForm($sexe)));
        }

        if ($dateNaissance = $request->get('dateNaissance')){
            $scout->setDateNaissance($dateNaissance);
        }

        if ($lieuNaissance = $request->get('lieuNaissance')){
            $scout->setLieuNaissance($lieuNaissance);
        }

        if ($telephone = $request->get('telephone')){
            $scout->setTelephone($telephone);
        }

        if ($email = $request->get('email')){
            $scout->setEmail($email);
        }

        if ($fonction = $request->get('fonction')){
            $scout->setFonction($fonction);
        }

        if ($branche = $request->get('branche')){
            $scout->setBranche($branche);
        }

        if ($statut = $request->get('statut')){
            $scout->setStatut($statut);
        }

        if ($telephoneParent = $request->get('telephoneParent')){
            $scout->setTelephoneParent($telephoneParent);
        }
        
        return $scout;
    }

    private function processUploadedPhoto($request, object $scout): void
    {
        $photoFile = $request->files->get('photo');
        if ($photoFile){
            if ($oldPhoto = $scout->getPhoto()){
                $this->gestionMedia->removeUpload($oldPhoto, 'profile');
            }

            $photo = $this->gestionMedia->upload($photoFile, 'profile');
            $scout->setPhoto($photo);
        }
    }
}
