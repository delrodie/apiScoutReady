<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\DistrictOutput;
use App\Entity\District;
use App\Service\AllRepositories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DistrictProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AllRepositories $allRepositories,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Suppression du district
        if ($operation->getMethod() === 'DELETE' && !empty($uriVariables['id'])) {
            $district = $this->allRepositories->getOneDistrict($uriVariables['id']);
            if (!$district) throw new NotFoundHttpException("Impossimble de surpprimer le district avec l'ID {$uriVariables['id']}.");

            $this->entityManager->remove($district);
            $this->entityManager->flush();

            return null;
        }

        // Modification et sauvegarde
        if (isset($uriVariables['id'])) {
            $district = $this->allRepositories->getOneDistrict($uriVariables['id']);
            if (!$district) throw new NotFoundHttpException("Impossible de modifier le district concerné par l'ID {$uriVariables['id']}, car il n'a pas été trouvé.é");
        }else{
            $district = new District();
        }

        $region = $this->allRepositories->getOneRegion($data->region);
        if (!$region) throw new NotFoundHttpException("Region introuvable avec l'ID {$data->region} !");

        $district->setNom($data->nom);
        $district->setRegion($region);

        $this->entityManager->persist($district);
        $this->entityManager->flush();

        return DistrictOutput::mapToOut($district);
    }
}
