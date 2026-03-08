<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\TokenJWTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthJwtController extends AbstractController
{
    #[Route('/auth/jwt', name: 'app_auth_jwt', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        TokenJWTService $tokenService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data['username'], $data['password'])) {
            return $this->json(['message' => 'user or password missing.'], 400);
        }

        //$user = $userRepository->findOneBy(['username' => $data['username']]);

        //if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
        //    return $this->json(['message' => 'Authentication failed.'], 401);
        //}

        $token = $tokenService->createFromUserId(4);
        $verif = $tokenService->getUserIdFromToken($token);

        return $this->json([
            'token' => $token,
            'verif' => $verif,
        ]);
    }
}