<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationTypeRepository::class)]
#[ORM\Table(name: 'location_type')]
class LocationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: false,
    )]
    public ?string $name = null;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        options: ['default' => 'CURRENT_TIMESTAMP']
    )]
    public \DateTimeImmutable $createdAt;

    #[ORM\Column(
        name: 'updated_at',
        type: Types::DATETIME_IMMUTABLE,
        options: ['default' => 'CURRENT_TIMESTAMP']
    )]
    public \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Explicit setter to ensure Doctrine Proxy tracks the change
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
