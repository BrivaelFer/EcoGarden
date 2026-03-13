<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Entity\Mois;
use App\Repository\ConseilRepository;
use App\Repository\MoisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ConseilController extends AbstractController
{
    #[Route('/conseil', name: 'api_conseil', methods:'GET')]
    public function getConseil(MoisRepository $moisRepository, SerializerInterface $serializer): JsonResponse
    {
        $id = date('n');

        $mois = $moisRepository->find($id);
        $conseils = $mois->getConseils();
        $conseils = $serializer->serialize($conseils, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return JsonResponse::fromJsonString($conseils);
    }

    #[Route('/conseil/{id}', name: 'api_conseil_mois', methods:'GET')]
    public function getConseilByMois(Mois $mois, SerializerInterface $serializer): JsonResponse
    {
        $conseils = ($mois->getConseils());
        $conseils = $serializer->serialize($conseils, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return JsonResponse::fromJsonString($conseils);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil', name: 'api_create_conseil', methods:'POST')]
    public function createConseil(
        Request $request,
        SerializerInterface $serializer, 
        EntityManagerInterface $em, 
        ValidatorInterface $validator
    ): JsonResponse
    {
        $dataRaw = $request->getContent();
        if(empty($dataRaw))  throw new HttpException(400,'Body vide');
        
        $idsMois = json_decode($dataRaw, true)['moisList'] ?? [];
        $conseil = $serializer->deserialize($dataRaw, Conseil::class, 'json');

        $conseil->setMoisList(new ArrayCollection());
        foreach ($idsMois as $id) {
            $mois = $em->getRepository(Mois::class)->find($id);
            if ($mois) {
                $conseil->addMoisList($mois);
                $mois->addConseil($conseil);
                $em->persist($mois);
            }
        }

        $errors = $validator->validate($conseil);

        if($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($conseil);
        $em->flush();

        return $this->json([
            'message' => 'conseil enregistré', 
            'id'=> $conseil->getId()
        ], JsonResponse::HTTP_CREATED);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil/{id}', name: 'api_put_conseil', methods:'PUT')]
    public function updateConseil(
        Conseil $conseil,
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em, 
        ValidatorInterface $validator
    ): JsonResponse
    {
        $idConseil = $conseil->getId();
        $data = json_decode($request->getContent(), true);
        if(!isset($data['title']) && !isset($data['content']) && !isset($data['moisList'])) {
            throw new HttpException(400,'Aucun champ à modifier');
        }
        $idsMois = $data['moisList'] ?? false;

        $conseil = $serializer->deserialize($request->getContent(), Conseil::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $conseil
        ]);
        
        if($idsMois !== false) {
            $conseil->setMoisList(new ArrayCollection());
            foreach ($idsMois as $id) {
                $mois = $em->getRepository(Mois::class)->find($id);
                if ($mois) {
                    $conseil->addMoisList($mois);
                }
            }
        }

        $errors = $validator->validate($conseil);

        if($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        foreach($conseil->getMoisList() as $mois) { 
            $mois->addConseil($conseil);
            $em->persist($mois);
        }
        $em->persist($conseil);
        
        $em->flush();

         return $this->json([
            'message' => 'Conseil ' . $idConseil . ' modifié',
        ], JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil/{id}', name: 'api_del_conseil', methods:'DELETE')]
    public function deleteConseil(Conseil $conseil, EntityManagerInterface $em): JsonResponse
    {
        $title = $conseil->getTitle();

        $em->remove($conseil);
        $em->flush();

        return $this->json([
            'message' => 'Conseil ' . $title . ' supprimé avec succès',
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
