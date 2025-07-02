<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\DTO\ComplementaireInput;
use App\DTO\ComplementaireOutput;
use App\Repository\ComplementaireRepository;
use App\State\ComplementaireProcessor;
use App\State\ComplementaireProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplementaireRepository::class)]
#[ApiResource(
    operations: [
        new Post(),
        new Get(),
        new GetCollection(),
        new Patch(),
        new Delete()
    ],
    inputFormats: ['json' => ['application/json', 'application/ld+json']],
    outputFormats: ['json' => ['application/json', 'application/ld+json']],
    input: ComplementaireInput::class,
    output: ComplementaireOutput::class,
    provider: ComplementaireProvider::class,
    processor: ComplementaireProcessor::class
)]
class Complementaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brancheOrigine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $baseNiveau1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeBaseNiveau1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $baseNiveau2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeBaseNiveau2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avanceNiveau1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeAvanceNiveau1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avanceNiveau2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeAvanceNiveau2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avanceNiveau3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeAvanceNiveau3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avanceNiveau4 = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeAvanceNiveau4 = null;

    #[ORM\OneToOne(inversedBy: 'complementaire', cascade: ['persist', 'remove'])]
    private ?Scout $scout = null;

    #[ORM\Column(nullable: true)]
    private ?bool $formation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrancheOrigine(): ?string
    {
        return $this->brancheOrigine;
    }

    public function setBrancheOrigine(?string $brancheOrigine): static
    {
        $this->brancheOrigine = $brancheOrigine;

        return $this;
    }

    public function getBaseNiveau1(): ?string
    {
        return $this->baseNiveau1;
    }

    public function setBaseNiveau1(?string $baseNiveau1): static
    {
        $this->baseNiveau1 = $baseNiveau1;

        return $this;
    }

    public function getAnneeBaseNiveau1(): ?int
    {
        return $this->anneeBaseNiveau1;
    }

    public function setAnneeBaseNiveau1(?int $anneeBaseNiveau1): static
    {
        $this->anneeBaseNiveau1 = $anneeBaseNiveau1;

        return $this;
    }

    public function getBaseNiveau2(): ?string
    {
        return $this->baseNiveau2;
    }

    public function setBaseNiveau2(?string $baseNiveau2): static
    {
        $this->baseNiveau2 = $baseNiveau2;

        return $this;
    }

    public function getAnneeBaseNiveau2(): ?int
    {
        return $this->anneeBaseNiveau2;
    }

    public function setAnneeBaseNiveau2(?int $anneeBaseNiveau2): static
    {
        $this->anneeBaseNiveau2 = $anneeBaseNiveau2;

        return $this;
    }

    public function getAvanceNiveau1(): ?string
    {
        return $this->avanceNiveau1;
    }

    public function setAvanceNiveau1(?string $avanceNiveau1): static
    {
        $this->avanceNiveau1 = $avanceNiveau1;

        return $this;
    }

    public function getAnneeAvanceNiveau1(): ?int
    {
        return $this->anneeAvanceNiveau1;
    }

    public function setAnneeAvanceNiveau1(?int $anneeAvanceNiveau1): static
    {
        $this->anneeAvanceNiveau1 = $anneeAvanceNiveau1;

        return $this;
    }

    public function getAvanceNiveau2(): ?string
    {
        return $this->avanceNiveau2;
    }

    public function setAvanceNiveau2(?string $avanceNiveau2): static
    {
        $this->avanceNiveau2 = $avanceNiveau2;

        return $this;
    }

    public function getAnneeAvanceNiveau2(): ?int
    {
        return $this->anneeAvanceNiveau2;
    }

    public function setAnneeAvanceNiveau2(?int $anneeAvanceNiveau2): static
    {
        $this->anneeAvanceNiveau2 = $anneeAvanceNiveau2;

        return $this;
    }

    public function getAvanceNiveau3(): ?string
    {
        return $this->avanceNiveau3;
    }

    public function setAvanceNiveau3(?string $avanceNiveau3): static
    {
        $this->avanceNiveau3 = $avanceNiveau3;

        return $this;
    }

    public function getAnneeAvanceNiveau3(): ?int
    {
        return $this->anneeAvanceNiveau3;
    }

    public function setAnneeAvanceNiveau3(?int $anneeAvanceNiveau3): static
    {
        $this->anneeAvanceNiveau3 = $anneeAvanceNiveau3;

        return $this;
    }

    public function getAvanceNiveau4(): ?string
    {
        return $this->avanceNiveau4;
    }

    public function setAvanceNiveau4(?string $avanceNiveau4): static
    {
        $this->avanceNiveau4 = $avanceNiveau4;

        return $this;
    }

    public function getAnneeAvanceNiveau4(): ?int
    {
        return $this->anneeAvanceNiveau4;
    }

    public function setAnneeAvanceNiveau4(?int $anneeAvanceNiveau4): static
    {
        $this->anneeAvanceNiveau4 = $anneeAvanceNiveau4;

        return $this;
    }

    public function getScout(): ?Scout
    {
        return $this->scout;
    }

    public function setScout(?Scout $scout): static
    {
        $this->scout = $scout;

        return $this;
    }

    public function isFormation(): ?bool
    {
        return $this->formation;
    }

    public function setFormation(?bool $formation): static
    {
        $this->formation = $formation;

        return $this;
    }
}
