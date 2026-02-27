<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\SavePersonDto;
use App\Repository\LocationTypeRepository;
use App\Repository\PersonRepository;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class PeopleController extends AbstractController
{
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly LocationTypeRepository $locationTypeRepository,
        private readonly TaskService $taskService
    ) {
    }

    #[Route('/people', name: 'app_people', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('people/index.html.twig', [
            'people' => $this->personRepository->findAllWithRelations(),
            'locationTypes' => $this->locationTypeRepository->findAll(),
        ]);
    }

    #[Route('/people/save', name: 'app_people_save', methods: ['POST'])]
    public function save(#[MapRequestPayload] SavePersonDto $dto): JsonResponse
    {
        return $this->taskService->savePerson($dto);
    }
}
