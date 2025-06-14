<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

class ScoutInput implements GroupSequenceProviderInterface
{
    //public ?string $code = null;
    public ?string $matricule = null;
    
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide", groups: ['Default', 'post'])]
    public ?string $nom = null;

    #[Assert\NotBlank(message: "Le prenom ne peut pas être vide", groups: ['Default', 'post'])]
    public ?string $prenom = null;

    #[Assert\NotBlank(message: "Le sexe ne peut pas être vide", groups: ['Default', 'post'])]
    public ?string $sexe = null;

    #[Assert\Date(message: "La date de naissance doit être au format Date", groups: ['Default', 'post'])]
    public ?string $dateNaissance = null;
    
    #[Assert\NotBlank(message: "Le lieu de naissance ne peut pas être vide", groups: ['Default', 'post'])]
    public ?string $lieuNaissance = null;

    #[Assert\NotBlank(message: "Le numéro de telephone ne peut être vide", groups: ['Default', 'post'])]
    #[Assert\Regex(
        pattern: "/^\d{10}$/",
        message: "Le numéro de téléphone doit contenir exactement 10 chiffres."
    )]
    public ?string $telephone = null;

    #[Assert\Email(message: "L'email n'est pas valide", groups: ['Default', 'post'])]
    public ?string $email = null;

    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg','image/jpg', 'image/png', 'image/webp'],
        mimeTypesMessage: "Merci d'uploader image JPEG ou PNG valide."
    )]
    public ?UploadedFile $photo = null;
    public ?string $fonction = null;
    public ?string $branche = null;

    #[Assert\NotBlank(message: 'Le statut ne peut être null', groups: ['Default', 'post'])]
    public ?string $statut = null;

    public ?bool $telephoneParent = null;

    #[Assert\NotBlank(message: "Le groupe est requis", groups: ['Default', 'post'])]
    public ?int $groupe = null;

    public function getGroupSequence(): array|GroupSequence
    {
        return ['Default', 'post'];
    }
}