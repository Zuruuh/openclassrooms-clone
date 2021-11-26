<?php

namespace App\Controller\Api;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    #[Route('/login')]
    public function loginAction(Request $request): JsonResponse
    {
        return $this->authService->loginAction($request);
    }

    #[Route('/register', methods: ['POST'])]
    public function registerAction(Request $request): JsonResponse
    {
        return $this->authService->registerAction($request);
    }

    #[Route('/forgot-password', methods: ['POST'])]
    public function forgotPasswordAction(Request $request): JsonResponse
    {
        return $this->authService->forgotPasswordAction($request);
    }

    #[Route('/validate-token', methods: ['GET'])]
    public function validateTokenAction(Request $request): JsonResponse
    {
        return $this->authService->validateTokenAction($request);
    }

    #[Route('/reset-password-from-token', methods: ['POST'])]
    public function resetPasswordFromTokenAction(Request $request): JsonResponse
    {
        return $this->authService->resetPasswordFromTokenAction($request);
    }
}
