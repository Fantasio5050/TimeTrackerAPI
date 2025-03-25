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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\Request;
final class AuthController extends AbstractController
{

    #[Route('/api/register', name: 'app_auth_register', methods: ['POST', 'GET'])]
    public function register(Request $request, UserRepository $userRepository, Security $security, EntityManagerInterface $entityManager): Response
    {
        $userMail = $request->request->get('email');
        if ($userRepository->findBy(['email' => $userMail])) {
            return new JsonResponse([
                'error' => 'User already exists with this email',
            ], Response::HTTP_CONFLICT);
        }

        try {
            die(dump(json_encode($request->request->all())));
            $user = new User();
            $user->setEmail($userMail);
            $user->setPassword($request->request->get('password'));
            $user->setName($request->request->get('name'));
            $user->setCreatedAt(new \DateTimeImmutable('now'));
            $user->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($user);
            $entityManager->flush();

            // Generate JWT
            $jwt = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

            // Return created user info and JWT
            return new JsonResponse([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt(),
                'jwt' => $jwt,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Error creating user: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/login', name: 'app_auth_login')]
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        $jwt = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        // Return the JWT
        return new JsonResponse([
            'jwt' => $jwt,
        ]);

    }
}
