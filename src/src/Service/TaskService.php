<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\SavePersonDto;
use App\Entity\Location;
use App\Entity\Person;
use App\Repository\LocationTypeRepository;
use App\Repository\MenuRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskService
{
    public function __construct(
        private readonly MenuRepository $menuRepository,
        private readonly PersonRepository $personRepository,
        private readonly LocationTypeRepository $locationTypeRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getMenuItems(): array
    {
        $records = $this->menuRepository->findBy(['isActive' => true], ['sortOrder' => 'ASC']);

        $menuItems = [];
        $menuItemsTmp = [];

        foreach ($records as $item) {
            $menuItemsTmp[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'children' => [],
                'parentId' => $item->childOfId?->id ?? null
            ];
        }

        foreach ($menuItemsTmp as $id => &$item) {
            if ($item['parentId'] === null) {
                $menuItems[] = &$item;
            } else {
                if (isset($menuItemsTmp[$item['parentId']])) {
                    $menuItemsTmp[$item['parentId']]['children'][] = &$item;
                }
            }
        }

        return array_values($menuItems);
    }

    public function savePerson(SavePersonDto $dto): JsonResponse
    {
        $this->entityManager->beginTransaction();

        try {
            $person = null;

            if (!empty($dto->id)) {
                $person = $this->personRepository->find((int)$dto->id);
            }

            if (!$person) {
                $person = new Person();
            }

            $person->firstName = $dto->firstName;
            $person->surName = $dto->surName;

            if ($person->id) {
                foreach ($person->getLocations() as $existingLocation) {
                    $this->entityManager->remove($existingLocation);
                }
                $this->entityManager->flush();
            }

            if (!empty($dto->locations)) {
                foreach ($dto->locations as $locData) {
                    $type = $this->locationTypeRepository->find((int)$locData->typeId);

                    if (!$type) {
                        continue;
                    }

                    $location = new Location();
                    $location->name = $locData->name;
                    $location->setLocationType($type);

                    $person->addLocation($location);
                }
            }

            $this->entityManager->persist($person);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return new JsonResponse([
                'success' => true,
                'message' => 'Saved successfully!',
                'person' => $this->formatPersonData($person),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->rollback();
            }

            return new JsonResponse([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function formatPersonData(Person $person): array
    {
        $locations = [];
        foreach ($person->getLocations() as $location) {
            $locations[] = [
                'name' => $location->name,
                'type' => $location->locationType?->name ?? 'Unknown',
                'typeId' => $location->locationType?->id,
            ];
        }

        return [
            'id' => $person->id,
            'firstName' => $person->firstName,
            'surName' => $person->surName,
            'locations' => $locations,
        ];
    }
}
