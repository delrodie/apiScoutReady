<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Asn;
use App\Service\AllRepositories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AsnProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private AllRepositories $allrepositories)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?Asn
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

        $asn->setNom($data->nom);
        $asn->setSigle($data->sigle);

        $this->entityManager->persist($asn);
        $this->entityManager->flush();

        return $asn;
    }
}
