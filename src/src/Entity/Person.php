<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\Table(name: 'person')]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(
        name: 'firstname',
        type: Types::STRING,
        length: 255,
        nullable: false,
    )]
    public ?string $firstName = null;

    #[ORM\Column(
        name: 'surname',
        type: Types::STRING,
        length: 255,
        nullable: false,
    )]
    public ?string $surName = null;

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

    /**
     * @var Collection<int, Location>
     */
    #[ORM\OneToMany(
        targetEntity: Location::class,
        mappedBy: 'person',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setPerson($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->person === $this) {
                $location->setPerson(null);
            }
        }

        return $this;
    }
}
