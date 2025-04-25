<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\DTO\ApiClientInput;
use App\DTO\ApiClientOutput;
use App\Repository\ApiClientRepository;
use App\State\ApiClientProcessor;
use App\State\ApiClientProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ApiClientRepository::class)]
#[ApiResource(
    operations: [
        new Post(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Get(),
        new GetCollection(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Patch(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Delete(security: "is_granted('ROLE_SUPER_ADMIN')")
    ],
    inputFormats: ['json' => ['application/json', 'application/ld+json']],
    outputFormats: ['json' => ['application/json', 'application/ld+json']],
    input: ApiClientInput::class,
    output: ApiClientOutput::class,
    provider: ApiClientProvider::class,
    processor: ApiClientProcessor::class
)]
class ApiClient implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $apiKey = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?array $roles = null;

    public function __construct()
    {
        $this->apiKey = bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles ?? ['ROLE_API'];
    }

    public function setRoles(?array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->apiKey;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }
}
