<?php

namespace App\Controller;

use App\Entity\Conseil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ConseilController extends AbstractController
{
    #[Route('/conseil', name: 'api_conseil', methods:'GET')]
    public function getConseil(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ConseilController.php',
        ]);
    }

    #[Route('/conseil/{mouth}', name: 'api_conseil_mouth', methods:'GET')]
    public function getConseilByMouth(int $mouth): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ConseilController.php',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil', name: 'api_create_conseil', methods:'POST')]
    public function createConseil(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ConseilController.php',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil/{id}', name: 'api_put_conseil', methods:'PUT')]
    public function updateConseil(Conseil $conseil): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ConseilController.php',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil/{id}', name: 'api_del_conseil', methods:'DELETE')]
    public function deleteConseil(Conseil $conseil): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ConseilController.php',
        ]);
    }
}
