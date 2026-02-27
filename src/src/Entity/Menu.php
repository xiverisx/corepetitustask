<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: 'menu')]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(
        targetEntity: self::class,
        inversedBy: 'children'
    )]
    #[ORM\JoinColumn(
        name: 'child_of_id',
        referencedColumnName: 'id',
        nullable: true,
        onDelete: 'CASCADE'
    )]
    public ?self $childOfId = null;

    #[ORM\Column(
        type: Types::STRING,
        length: 255,
        nullable: false,
    )]
    public string $name = '';

    #[ORM\Column(
        name: 'sort_order',
        type: Types::INTEGER,
        nullable: false
    )]
    public int $sortOrder = 0;

    #[ORM\Column(
        name: 'is_active',
        type: Types::BOOLEAN,
        options: ['default' => true]
    )]
    public bool $isActive = true;

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
     * @var Collection<int, Menu>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'childOfId')]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    public Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }
}
