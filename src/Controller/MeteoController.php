<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MeteoController extends AbstractController
{

    private string $apiUrl;
    private string $apiKey;

    public function __construct(
    )
    {
        $this->apiUrl = $_ENV['METEO_URL'];
        $this->apiKey = $_ENV['METEO_KEY'];
    }

    #[Route('/meteo', name: 'app_meteo')]
    public function getUserMeteo(HttpClientInterface $httpClient, TagAwareCacheInterface $tagAwareCacheInterface): JsonResponse
    {
        /** @var User */
        $user = $this->getUser();
        $ville = $user->getVille();

        $data = $this->getData($tagAwareCacheInterface, $httpClient, $ville);

        if($data['code'] == 404) {
            $data['content'] = [
                'erreur' => 404,
                'message' => 'Aucune données trouver pour la ville de l\'utilisateur'
            ];
        }

        return $this->json($data['content'], $data['code']);
    }

    #[Route('/meteo/{ville}', name: 'app_meteo_ville')]
    public function getvilleMeteo(string $ville, HttpClientInterface $httpClient, TagAwareCacheInterface $tagAwareCacheInterface): JsonResponse
    {
        $data = $this->getData($tagAwareCacheInterface, $httpClient, $ville);

        if($data['code'] == 404) {
            $data['content'] = [
                'erreur' => 404,
                'message' => 'Aucune données trouver pour la ville : ' . $ville
            ];
        }

        return $this->json($data['content'], $data['code']);
    }

    private function getData(TagAwareCacheInterface $tagAwareCacheInterface, HttpClientInterface $httpClient, string $ville): array
    {
        $idCache = "meteo-{$ville}";
        return $tagAwareCacheInterface->get($idCache, function (ItemInterface $item) use ($httpClient, $ville) {
            $item->tag("meteoCache");
            $item->expiresAfter(3600);
            return $this->apiCall($ville, $httpClient);
        });
    }

    private function apiCall(string $ville, HttpClientInterface $httpClient): array
    {

        $response = $httpClient->request('GET', $this->apiUrl, [
            'query' => [
                'q' => $ville,
                'appid' => $this->apiKey,
                'units' => 'metric',
            ],
        ]);

        $code = $response->getStatusCode();
        if ($code == 404) {
            return [
                'code' => $code
            ];
        }

        $content = json_decode($response->getContent(), true);

        return [
            'code' => $code,
            'content' => $content
        ];
    }
}
