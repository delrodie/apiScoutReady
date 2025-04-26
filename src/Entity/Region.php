<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\DTO\RegionInput;
use App\DTO\RegionOutput;
use App\Repository\RegionRepository;
use App\State\RegionProcessor;
use App\State\RegionProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ApiResource(
    operations: [
        new Post(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Get(),
        new GetCollection(),
        new Patch(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Delete(security: "is_granted('ROLE_SUPER_ADMIN')")
    ],
    inputFormats: ['json' => ['application/json', 'application/ld+json']],
    outputFormats: ['json' => ['application/json', 'application/ld+json']],
    input: RegionInput::class,
    output: RegionOutput::class,
    provider: RegionProvider::class,
    processor: RegionProcessor::class,
)]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $symbolique = null;

    #[ORM\ManyToOne]
    private ?Asn $asn = null;

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

    public function getSymbolique(): ?string
    {
        return $this->symbolique;
    }

    public function setSymbolique(?string $symbolique): static
    {
        $this->symbolique = $symbolique;

        return $this;
    }

    public function getAsn(): ?Asn
    {
        return $this->asn;
    }

    public function setAsn(?Asn $asn): static
    {
        $this->asn = $asn;

        return $this;
    }
}
