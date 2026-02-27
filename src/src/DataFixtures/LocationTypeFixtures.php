<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\LocationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocationTypeFixtures extends Fixture
{
    public const string TYPE_REFERENCE = 'location_type_';

    public function load(ObjectManager $manager): void
    {
        $types = ['home', 'work', 'postal', 'other'];

        foreach ($types as $index => $type) {
            $locationType = new LocationType();
            $locationType->name = $type;

            $manager->persist($locationType);

            $this->addReference(self::TYPE_REFERENCE.$index, $locationType);
        }

        $manager->flush();
    }
}
