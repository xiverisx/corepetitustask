<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    public function __construct(
        private readonly TaskService $taskService
    ) {
    }

    #[Route('/', name: 'app_menu')]
    public function index(): Response
    {
        return $this->render('menu/index.html.twig', [
            'menu_items' => $this->taskService->getMenuItems(),
        ]);
    }
}
