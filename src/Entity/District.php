<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\DTO\DistrictInput;
use App\DTO\DistrictOutput;
use App\Repository\DistrictRepository;
use App\State\DistrictProcessor;
use App\State\DistrictProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DistrictRepository::class)]
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
    input: DistrictInput::class,
    output: DistrictOutput::class,
    provider: DistrictProvider::class,
    processor: DistrictProcessor::class,
)]
#[ApiFilter(SearchFilter::class, properties: ['region.id' => 'exact'])]
class District
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    private ?Region $region = null;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }
}
