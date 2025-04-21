<?php

namespace App\Service;

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
        //CF2504182554 SC2504186547
        $initial = $statut === 'ADULTE' ? 'CF' : 'SC';
        do{
            $variable = str_pad((int)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $code = $initial.date('ymd') . $variable;
        }while($this->allRepositories->getOneScout(null, $code));

        return $code;
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