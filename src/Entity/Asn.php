<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\DTO\AsnInput;
use App\DTO\AsnOutput;
use App\Repository\AsnRepository;
use App\State\AsnProcessor;
use App\State\AsnProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsnRepository::class)]
#[ApiResource(
    operations: [
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Get(),
        new GetCollection(),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    inputFormats: ['json' => ['application/json', 'application/ld+json']],
    outputFormats: ['json' => ['application/json', 'application/ld+json']],
    input: AsnInput::class,
    output: AsnOutput::class,
    provider: AsnProvider::class,
    processor: AsnProcessor::class
)]
class Asn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sigle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): static
    {
        $this->sigle = $sigle;

        return $this;
    }
}
