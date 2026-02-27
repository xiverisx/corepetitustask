<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ORM\Table(name: 'location')]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'locations')]
    #[ORM\JoinColumn(
        name: 'person_id',
        referencedColumnName: 'id',
        nullable: false
    )]
    public ?Person $person = null;

    #[ORM\ManyToOne(targetEntity: LocationType::class)]
    #[ORM\JoinColumn(
        name: 'location_type_id',
        referencedColumnName: 'id',
        nullable: false
    )]
    public ?LocationType $locationType = null;

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

    public function setPerson(?Person $person): self
    {
        $this->person = $person;
        return $this;
    }

    public function setLocationType(?LocationType $locationType): self
    {
        $this->locationType = $locationType;
        return $this;
    }
}
