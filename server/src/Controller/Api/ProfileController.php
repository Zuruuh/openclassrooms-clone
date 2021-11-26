<?php

namespace App\Controller\Api;

use App\Service\ProfileService;
use App\Controller\Api\ProtectedRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/profile')]
class ProfileController extends AbstractController implements ProtectedRoute
{
    private ProfileService $profileService;

    public function __construct(
        ProfileService $profileService
    ) {
        $this->profileService = $profileService;
    }


    #[Route('/', methods: ['GET'])]
    public function getOwnProfileAction(Request $request): JsonResponse
    {
        return $this->profileService->getOwnProfileAction($request);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getProfileAction(mixed $id, Request $request): JsonResponse
    {
        return $this->profileService->getProfileAction($request, (int) $id);
    }

    #[Route('/edit', methods: ['PATCH'])]
    public function editProfileAction(Request $request): JsonResponse
    {
        return $this->profileService->editProfileAction($request);
    }
}
