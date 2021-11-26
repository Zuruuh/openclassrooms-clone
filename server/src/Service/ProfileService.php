<?php

namespace App\Service;

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProfileService
{
    private JwtService             $jwtService;
    private NormalizerInterface    $normalizer;
    private UserService            $userService;
    private EntityManagerInterface $em;

    public function __construct(
        JwtService             $jwtService,
        NormalizerInterface    $normalizer,
        UserService            $userService,
        FormService            $formService,
        EntityManagerInterface $em,
    ) {
        $this->jwtService  = $jwtService;
        $this->normalizer  = $normalizer;
        $this->userService = $userService;
        $this->formService = $formService;
        $this->em          = $em;
    }

    public function getOwnProfileAction(Request $request): JsonResponse
    {
        $user = $this->jwtService->getUserFromRequest($request);
        $profile = $user->getProfile();

        $profile = $this->normalizer->normalize($profile, null, ['groups' => ['display']]);

        return new JsonResponse(['profile' => $profile]);
    }

    public function getProfileAction(Request $_request, int $id): JsonResponse
    {
        $user = $this->userService->exists(['id' => $id]);
        $profile = $user->getProfile();
        $profile = $this->normalizer->normalize($profile, null, ['groups' => ['display']]);

        return new JsonResponse(['profile' => $profile]);
    }

    public function editProfileAction(Request $request): JsonResponse
    {
        $user = $this->jwtService->getUserFromRequest($request);
        $profile = $this->formService->handleForm(
            ProfileFormType::class,
            $request,
            $user->getProfile()
        );

        $this->em->persist($profile);
        $this->em->flush();

        return new JsonResponse();
    }
}
