<?php

namespace App\Controller\Api;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    #[Route('/api/login')]
    public function loginAction(Request $request): JsonResponse
    {
        return $this->authService->loginAction($request);
    }

    #[Route('/api/register', methods: ['POST'])]
    public function registerAction(Request $request): JsonResponse
    {
        return $this->authService->registerAction($request);
    }
}
