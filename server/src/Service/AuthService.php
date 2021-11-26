<?php

namespace App\Service;

use App\Entity\Profile;
use App\Entity\ResetUserPasswordToken;
use App\Entity\User;
use App\Form\ForgotPasswordFormType;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFromTokenFormType;
use App\Repository\ResetUserPasswordTokenRepository as TokenRepo;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as Hasher;

class AuthService
{
    private Hasher $hasher;
    private EntityManagerInterface      $em;
    private MailerInterface             $mailer;
    private UserRepository              $userRepo;
    private FormService                 $formService;
    private JwtService                  $jwtService;
    private TokenRepo                   $tokenRepo;

    public const INVALID_CREDENTIALS     = 'Invalid credentials';
    public const FORGOT_PASSWORD_MESSAGE = 'Openclassrooms Clone Password reset';
    public const FORGOT_PASSWORD_MAIL    = '<p>Hello, you have requested to reset your password, to do so, click <a href="%s">here</a></p>';
    public const INVALID_TOKEN           = 'Your reset password token is invalid. Make sure you specified it in the ?token query param';

    public function __construct(
        EntityManagerInterface      $em,
        MailerInterface             $mailer,
        UserRepository              $userRepo,
        FormService                 $formService,
        JwtService                  $jwtService,
        TokenRepo                   $tokenRepo,
        Hasher                      $hasher,
    ) {
        $this->formService = $formService;
        $this->jwtService  = $jwtService;
        $this->tokenRepo   = $tokenRepo;
        $this->userRepo    = $userRepo;
        $this->hasher      = $hasher;
        $this->mailer      = $mailer;
        $this->em          = $em;
    }

    /**
     * >>> Controllers Actions >>>
     */
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

    public function forgotPasswordAction(Request $request): JsonResponse
    {
        $email = $this->formService->handleForm(ForgotPasswordFormType::class, $request)['email'];
        $user = $this->userRepo->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(null, 204);
        }

        $token = $this->generateNewResetPasswordToken($user);
        $this->sendForgotPasswordEmail($email, $token->getToken());

        return new JsonResponse(null, 200);
    }

    public function validateTokenAction(Request $request): JsonResponse
    {
        $token = $this->validateToken($request->query->get('token'));

        return new JsonResponse(['valid' => (bool) $token]);
    }

    public function resetPasswordFromTokenAction(Request $request): JsonResponse
    {
        $token = $this->validateToken($request->query->get('token'));
        if (!$token) {
            throw new AccessDeniedHttpException(self::INVALID_TOKEN);
        }
        $password = $this->formService->handleForm(ResetPasswordFromTokenFormType::class, $request)['password'];
        $user = $token->getIssuer();

        $hashed = $this->hasher->hashPassword($user, $password);
        $user->setPassword($hashed);

        $this->em->persist($user);
        $this->em->remove($token);
        $this->em->flush();

        $token = $this->jwtService->generateJWT($user);

        return new JsonResponse(['token' => $token], 200);
    }

    /**
     * <<< Controllers Actions <<<
     */

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

        $profile = new Profile();
        $profile->setOwner($user);

        $this->em->persist($user);
        $this->em->persist($profile);

        $this->em->flush();
    }

    private function generateNewResetPasswordToken(User $user): ResetUserPasswordToken
    {
        $exists = $this->tokenRepo->findOneBy(['issuer' => $user]);
        if ($exists) {
            return $exists;
        }

        $chars = [
            ...range('a', 'z'),
            ...range('A', 'Z'),
            ...range('0', '9'),
        ];
        $token = '';
        for ($i = 0; $i < 128; $i++) {
            $token .= $chars[rand(0, sizeof($chars) - 1)];
        }

        $userToken = (new ResetUserPasswordToken())->setToken($token)->setIssuer($user);

        $this->em->persist($userToken);
        $this->em->flush();

        return $userToken;
    }

    private function sendForgotPasswordEmail(string $emailAddress, string $token): void
    {
        $content = sprintf(self::FORGOT_PASSWORD_MAIL, 'https://app.local/api/reset-password-from-token?token=' . $token);

        $email = (new Email())
            ->from('younesziadi@outlook.fr')
            ->to($emailAddress)
            ->subject(self::FORGOT_PASSWORD_MESSAGE)
            ->html($content);

        $this->mailer->send($email);
    }

    private function validateToken(string $token): ResetUserPasswordToken | null
    {
        $userToken = $this->tokenRepo->findOneBy(['token' => $token]);
        if (!$userToken) return null;

        $issuedAt = $userToken->getIssuedAt()->getTimestamp();

        if ($issuedAt + (60 * 60 * 2) < time()) {
            $this->em->remove($userToken);
            $this->em->flush();

            return null;
        }

        return $userToken;
    }
}
