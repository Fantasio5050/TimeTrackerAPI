<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\Request;
final class AuthController extends AbstractController
{

    #[Route('/api/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(Request $request, UserRepository $userRepository, Security $security, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data){
            return new JsonResponse([
                'error' => 'Invalid JSON',
            ], Response::HTTP_BAD_REQUEST);
        }

        $userMail = $data['email'];
        if ($userRepository->findBy(['email' => $userMail])) {
            return new JsonResponse([
                'error' => 'User already exists with this email',
            ], Response::HTTP_CONFLICT);
        }

        try {
            $user = new User();
            $user->setEmail($userMail);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            $user->setName($data['name']);
            $user->setCreatedAt(new \DateTimeImmutable('now'));
            $user->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($user);
            $entityManager->flush();


            // Return created user info and JWT
            return new JsonResponse([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'name' => $user->getName(),
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt(),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Error creating user: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
