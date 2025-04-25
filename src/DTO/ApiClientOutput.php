<?php

namespace App\DTO;

use App\Entity\ApiClient;

class ApiClientOutput
{
    public ?int $id = null;
    public ?string $apiKey = null;
    public ?string $name = null;
    public ?array $roles = null;

    public static function MapToOut(ApiClient $client): self
    {
        $dto = new self();
        $dto->id = $client->getId();
        $dto->name = $client->getName();
        $dto->apiKey = $client->getApiKey();
        $dto->roles = $client->getRoles();

        return $dto;
    }
}