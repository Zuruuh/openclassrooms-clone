<?php

namespace App\Service;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface      $em;
    private FormService                 $formService;
    private JwtService                  $jwtService;
    private UserRepository              $userRepo;

    public const INVALID_CREDENTIALS = 'Invalid credentials';

    public function __construct(
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface      $em,
        FormService                 $formService,
        JwtService                  $jwtService,
        UserRepository              $userRepo
    ) {
        $this->formService = $formService;
        $this->jwtService  = $jwtService;
        $this->hasher      = $hasher;
        $this->em          = $em;
        $this->userRepo    = $userRepo;
    }

    public function loginAction(Request $request): JsonResponse
    {
        extract($this->formService->handleForm(LoginFormType::class, $request)); // $login, $password
        $user = $this->getUserByLogin($login);
        $this->validatePassword($user, $password);

        $token = $this->jwtService->generateJWT($user);

        return new JsonResponse(['token' => $token]);
    }

    public function registerAction(Request $request): JsonResponse
    {
        $user = $this->formService->handleForm(RegistrationFormType::class, $request);
        $this->save($user);

        $token = $this->jwtService->generateJWT($user);

        return new JsonResponse(['token' => $token]);
    }

    private function getUserByLogin(string $login): User
    {
        $user = $this->userRepo->findOneBy(
            str_contains($login, '@')
                ? ['email' => $login]
                : ['username' => $login]
        );
        if (!$user) {
            throw new UnauthorizedHttpException('Bearer', self::INVALID_CREDENTIALS);
        }

        return $user;
    }

    private function validatePassword(User $user, string $password): void
    {
        if (!($this->hasher->isPasswordValid($user, $password))) {
            throw new UnauthorizedHttpException('Bearer', self::INVALID_CREDENTIALS);
        }
    }

    private function save(User $user): void
    {
        $password = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
    }
}
