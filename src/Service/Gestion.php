<?php

namespace App\Service;

use App\Entity\Utilisation;
use App\Repository\ScoutRepository;

class Gestion
{

    public function __construct(
        private AllRepositories $allRepositories,
    )
    {
    }

    public function generateCode(?string $statut): string
    {
        //CF2504182554-A4 SC2504186547-3C
        $prefix = $statut === 'ADULTE' ? 'CF' : 'SC';
        do{
            $variable = str_pad((int)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
//            $unique = strtoupper(bin2hex($variable));
            $base = $prefix.date('ymd').$variable;
            $checksum = strtoupper(substr(hash('crc32b', $base), 0, 2));
            $code = $base.'-'.$checksum;
        }while($this->allRepositories->getOneScout(null, $code));

        return $code;
    }

    public function verificationChecksum(string $code): bool
    {
        // Vérifie le format général
        if (!preg_match('/^[A-Z]{2}\d{6}\d{4}-[A-F0-9]{2}$/', $code)){
            return false;
        }

        // Separation des parties
        [$base, $checkum] = explode('-', $code);
        $expected = strtoupper(substr(hash('crc32b', $base), 0, 2));

        return $checkum === $expected;
    }

    public function saveUtilisation(?object $scout, ?array $param): Utilisation|false
    {
        $verif = $this->allRepositories->getOneUtilisation($scout->getId());
        if ($verif) return false;

        $utilisation = new Utilisation();
        $utilisation->setAnnee($this->annee());
        $utilisation->setScout($scout);
        $utilisation->setGroupe($param['groupe']);
        $utilisation->setStatut(Variables::UTILISATEUR_STATUT_ATTENTE);
        $utilisation->setDemandeur($param['demandeur']);

        return $utilisation;
    }

    public static function annee(): string
    {
        $anneeEncours = (int) Date('Y');
        $moisEncours = (int) Date('m');

        $debutAnnee = $moisEncours > 9 ? $anneeEncours : $anneeEncours - 1;
        $finAnnee = $moisEncours > 9 ? $anneeEncours + 1 : $anneeEncours;

        return sprintf('%d-%d', $debutAnnee, $finAnnee);
    }

    /**
     * @param $str
     * @return string
     */
    public function validForm($str): string
    {
        return htmlspecialchars(stripslashes(trim($str)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}