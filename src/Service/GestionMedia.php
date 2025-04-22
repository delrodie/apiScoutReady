<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GestionMedia
{
    private string $photoProfile;
    private string $photoActivite;

    public function __construct(
        string $profileDirectory, string $scoutsDirectory
    )
    {
        $this->photoProfile = $profileDirectory;
        $this->photoActivite = $scoutsDirectory;
    }

    /**
     * @param $form
     * @param object $entity
     * @param string $entityName
     * @return void
     */
    public function media($form, object $entity, string $entityName): void
    {
        // Gestion des photos de profile
        $mediaFile = $form->get('photo')->getData();
        if ($mediaFile){
            $media = $this->upload($mediaFile, $entityName);

            $entity->setMedia($media);
        }
    }


    /**
     * @param UploadedFile $file
     * @param $media
     * @return string
     */
    public function upload(UploadedFile $file, $media = null): string
    {
        // Initialisation du slug
        $slugify = new AsciiSlugger();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slug(strtolower($originalFileName));
        $newFilename = $safeFilename.'-'.Time().'.'.$file->guessExtension();

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'profile') $file->move($this->photoProfile, $newFilename);
            else $file->move($this->photoActivite, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;
    }

    /**
     * Suppression de l'ancien media sur le server
     *
     * @param $ancienMedia
     * @param null $media
     * @return bool
     */
    public function removeUpload($ancienMedia, $media = null): bool
    {
        if ($media === 'profile') unlink($this->photoProfile.'/'.$ancienMedia);
        elseif ($media === 'activite') unlink($this->photoActivite.'/'.$ancienMedia);
        else return false;

        return true;
    }
}