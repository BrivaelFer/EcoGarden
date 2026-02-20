<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    
    
    #[Route('/user', name: 'api_create_user', methods: 'POST')]
    public function createUser(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $dataRaw = $request->getContent();
        if(empty($dataRaw)) throw new HttpException(400,'Body vide');

        $user = $serializer->deserialize($dataRaw, User::class, 'json');

        if(!$this->isGranted('ROLE_ADMIN')) $user->setRole([]);

        $errors = $validator->validate($user);

        if($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        
        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'user enregistré'], 200);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('user/{id}', name: 'api_put_user', methods: 'PUT')]
    public function updateUser(
        User $user,
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if(!isset($data['email']) && !isset($data['role']) && !isset($data['password']) && !isset($data['ville'])) {
            throw new HttpException(400,'Aucun champ à modifier');
        }
        $currentPw = $user->getPassword();
        $id = $user->getId();

        /** @var User */
        $requestUser = $serializer->deserialize($request->getContent(), User::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user
        ]);

        $errors = $validator->validate($requestUser);

        if($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        if($currentPw !== $requestUser->getPassword()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        }

        $em->persist($requestUser);
        $em->flush();

        return $this->json([
            'message' => 'Utilisater ' . $id . ' modifié',
        ], 200);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('user/{id}', name: 'api_del_user', methods: 'DELETE')]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $email = $user->getEmail();

        $em->remove($user);
        $em->flush();

        return $this->json([
            'message' => 'Utilisateur ' . $email . ' supprimé avec succès',
        ], 200);
    }
}
