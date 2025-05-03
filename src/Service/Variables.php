<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Self_;

class Variables
{
    const MATRICULE ='MATRICULE';
    const CODE = 'CODE';
    const TELEPHONE = 'TELEPHONE';
    const GROUPE = 'GROUPE';
    const DISTRICT = 'DISTRICT';
    const REGION = 'REGION';
    const ASN = 'ASN';
    const PAGE = 'PAGE';

    // STATUT UTILISATEUR
    const UTILISATEUR_STATUT_ATTENTE = 0;
    const UTILISATEUR_STATUT_APPROUVE = 1;
    const UTILISATEUR_STATUT_REJETE = 2;

    public static function statutLibelle($statut): string
    {
        return match ($statut){
            self::UTILISATEUR_STATUT_ATTENTE => 'EN ATTENTE',
            self::UTILISATEUR_STATUT_REJETE => 'REJETEE',
            self::UTILISATEUR_STATUT_APPROUVE => 'VALIDEE',
            default => 'NON DEFINI'
        };
    }
}