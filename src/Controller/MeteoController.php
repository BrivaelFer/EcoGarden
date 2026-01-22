<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MeteoController extends AbstractController
{

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient
    )
    {
        
    }

    #[Route('/meteo', name: 'app_meteo')]
    public function getUserMeteo(): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();

        return $this->apiCall($user->getVille());
    }

    #[Route('/meteo/{ville}', name: 'app_meteo_ville')]
    public function getvilleMeteo(string $ville): JsonResponse
    {
        return $this->apiCall($ville);
    }

    private function apiCall(string $ville): JsonResponse
    {
        

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MeteoController.php',
        ]);
    }
}
