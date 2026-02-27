<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.locations', 'l')
            ->addSelect('l')
            ->leftJoin('l.locationType', 'lt')
            ->addSelect('lt')
            ->getQuery()
            ->getResult();
    }
}
