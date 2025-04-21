<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\AsnOutput;
use App\Entity\Asn;
use App\Service\AllRepositories;
use App\Service\Gestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AsnProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AllRepositories $allrepositories,
        private Gestion $gestion,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Gestion de la suppression
        if ($operation->getMethod() === 'DELETE' && isset($uriVariables['id'])) {
            $asn = $this->allrepositories->getOneAsn($uriVariables['id']);
            if (!$asn) throw new NotFoundHttpException("Impossible de surprimer l'ASN car l'identifiant: {$uriVariables['id']} n'a pas été trouvé.");

            $this->entityManager->remove($asn);
            $this->entityManager->flush();

            return null;
        }

        if (isset($uriVariables['id'])){
            $asn = $this->allrepositories->getOneAsn($uriVariables['id']);
            if (!$asn) throw new NotFoundHttpException("Impossible de modifier l'ASN car l'identifiant {$uriVariables['id']} n'a pas été trouvé!");
        }else{
            $asn = new Asn();
        }

        $asn->setNom($this->gestion->validForm($data->nom));
        $asn->setSigle(strtoupper($this->gestion->validForm($data->sigle)));

        $this->entityManager->persist($asn);
        $this->entityManager->flush();

        return AsnOutput::mapToOutput($asn);
    }
}
