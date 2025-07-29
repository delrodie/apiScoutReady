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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\Clock\now;

class ApiScoutPatchController extends AbstractController
{
    public function __construct(
        private readonly AllRepositories $allRepositories,
        private readonly Gestion $gestion,
        private readonly GestionMedia $gestionMedia,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(
        Request $request,
        $id,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse|ScoutOutput
    {
        // Récuperons le scout existant
       $reqScout = $this->allRepositories->getOneScout((int) $id);
       if (!$reqScout){
           return new JsonResponse(['error' => "Scout non trouvé"], 404);
       }

       // Assignons les données de la requête au scout
       $scout = $this->assignRequestData($request, $reqScout);

       // Traitons la photo uploadée
        $this->processUploadedPhoto($request, $scout); //dd($scout);

        // persistons les changements
        $this->entityManager->flush();

        // Retournons une réponse de succès
        return new JsonResponse([
            'statut' => "Mise à jour effectuée avec succès!"
        ], 200);
    }

    private function assignRequestData($request, object $scout): object
    {
        // Assurons-nous que $Scout est bien une instance d'App\Entity\Scout
        if(!$scout instanceof Scout){
            throw new \InvalidArgumentException("L'objet scout doit être une instance de App\entity\Scout");
        }

        if ($request->request->has('matricule')){
            $matricule = $request->request->get('matricule');
            $scout->setMatricule(strtoupper($this->gestion->validForm($matricule)));
        }

        if ($request->request->has('nom')){
            $nom = $request->request->get('nom');
            $scout->setNom(strtoupper($this->gestion->validForm($nom)));
        }

        if ($request->request->has('prenom')){
            $prenom = $request->request->get('prenom');
            $scout->setPrenom(strtoupper($this->gestion->validForm($prenom)));
        }

        if ($request->request->has('sexe')){
            $sexe = $request->request->get('sexe');
            $scout->setSexe(strtoupper($this->gestion->validForm($sexe)));
        }

        if ($request->request->has('dateNaissance')){
            $dateNaissance = $request->request->get('dateNaissance');
            $scout->setDateNaissance($this->parseDate($dateNaissance));
        }

        if ($request->request->has('lieuNaissance')){
            $lieuNaissance = $request->request->get('lieuNaissance');
            $scout->setLieuNaissance($lieuNaissance);
        }

        if ($request->request->has('telephone')){
            $telephone = $request->request->get('telephone');
            $scout->setTelephone($telephone);
        }

        if ($request->request->has('email')){
            $email = $request->request->get('email');
            $scout->setEmail($email);
        }

        if ($request->request->has('fonction')){
            $fonction = $request->request->get('fonction');
            $scout->setFonction($fonction);
        }

        if ($request->request->has('branche')){
            $branche = $request->request->get('branche');
            $scout->setBranche($branche);
        }

        if ($request->request->has('statut')){
            $statut = $request->request->get('statut');
            $scout->setStatut($statut);
        }

        if ($request->request->has('telephoneParent')){
            $telephoneParent = $request->get('telephoneParent');
            $scout->setTelephoneParent(filter_var($telephoneParent, FILTER_VALIDATE_BOOLEAN));
        }

        if($request->request->has('groupe')){
            $groupeId = (int) $request->request->get('groupe');
            $groupe = $this->allRepositories->getOneGroupe($groupeId);
            if (!$groupe){
                throw new NotFoundHttpException("Le groupe avec l'ID {$groupeId} n'a pas été trouvé");
            }
            $scout->setGroupe($groupe);
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
}
