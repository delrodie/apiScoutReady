<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\RegionOutput;
use App\Entity\Region;
use App\Service\AllRepositories;
use App\Service\Gestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegionProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AllRepositories $allRepositories,
        private Gestion $gestion,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Suppression de la region
        if ($operation->getMethod() === 'DELETE' && isset($uriVariables['id'])) {
            $region = $this->allRepositories->getOneRegion($uriVariables['id']);
            if (!$region) throw new NotFoundHttpException("Impossible de supprimer la region concernée par l'ID {$uriVariables['id']} car elle n'a pas été trouvée.");
            $this->entityManager->remove($region);
            $this->entityManager->flush();

            return null;
        }

        // Modification ou enregistrement
        if (isset($uriVariables['id'])){
            $region = $this->allRepositories->getOneRegion($uriVariables['id']);
            if (!$region) throw new NotFoundHttpException("Impossible de modifier la region concernée par l'ID {$uriVariables['id']} car elle n'a pas été trouvée.");
        }else{
            $region = new Region();
        }

        $asn = $this->allRepositories->getOneAsn($data->asn);
        if (!$asn) throw new NotFoundHttpException("ASN introuvable avec l'ID {$data->asn} !");

        $region->setAsn($asn);
        $region->setNom($this->gestion->validForm($data->nom));
        $region->setSymbolique($this->gestion->validForm($data->symbolique));

        $this->entityManager->persist($region);
        $this->entityManager->flush();

        return RegionOutput::mapToOut($region);
    }
}
