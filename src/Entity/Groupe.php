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
use App\DTO\GroupeInput;
use App\DTO\GroupeOutput;
use App\Repository\GroupeRepository;
use App\State\GroupeProcessor;
use App\State\GroupeProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
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
    input: GroupeInput::class,
    output: GroupeOutput::class,
    provider: GroupeProvider::class,
    processor: GroupeProcessor::class
)]
#[ApiFilter(SearchFilter::class, properties: ['district.id' => 'exact', 'region.id' => 'exact'])]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paroisse = null;

    #[ORM\ManyToOne]
    private ?District $district = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParoisse(): ?string
    {
        return $this->paroisse;
    }

    public function setParoisse(?string $paroisse): static
    {
        $this->paroisse = $paroisse;

        return $this;
    }

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): static
    {
        $this->district = $district;

        return $this;
    }
}
