<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    )
    {
        
    }
    
    #[Route('/user', name: 'api_create_user', methods: 'POST')]
    public function createUser(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller POST!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('user/{id}', name: 'api_put_user', methods: 'PUT')]
    public function updateUser(User $user): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller DELETE!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('user/{id}', name: 'api_del_user', methods: 'DELETE')]
    public function deleteUser(User $user): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller DELETE!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
