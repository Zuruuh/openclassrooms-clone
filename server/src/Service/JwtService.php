<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwtService
{
    private JWTTokenManagerInterface $jwtManager;
    private EntityManagerInterface   $em;
    private UserRepository           $userRepo;

    public const INVALID_TOKEN = 'This action needs a valid token!';

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EntityManagerInterface   $em,
        UserRepository           $userRepo
    ) {
        $this->jwtManager = $jwtManager;
        $this->userRepo   = $userRepo;
        $this->em         = $em;
    }

    /**
     * Validates a JsonWebToken.
     * 
     * @param string $token     The token to validate
     * @param bool   ?$throwing Should an error be thrown ?
     * 
     * @throws UnauthorizedHttpException If token is not valid & throwing is set to true
     * 
     * @return array|bool User|false
     */
    public function validateToken(string $token, bool $throwing = true): array|bool
    {
        try {
            $user = $this->jwtManager->parse(substr($token, 7));

            return $user;
        } catch (JWTDecodeFailureException $_) {
            if ($throwing) {
                throw new UnauthorizedHttpException('Bearer', self::INVALID_TOKEN);
            }

            return false;
        }
    }

    /**
     * Generates a JsonWebToken.
     * 
     * @param User $uesr The user who requested the JWT
     * 
     * @return string $jwt The generated JWT
     */
    public function generateJWT(User $user): string
    {
        return $this->jwtManager->create($user);
    }


    /**
     * Generates a new JsonWebToken from a request's payload
     * 
     * @param array $payload The request's payload
     * 
     * @return string|bool JWT|false
     */
    public function generateJWTFromPayload(array $payload): string|bool
    {
        $user = $this->userRepo->findOneBy(['username' => $payload['username']]);
        if ($user instanceof User) {
            return $this->generateJWT($user);
        }

        return false;
    }

    public function updateLastSeen(array $payload): void
    {
        $user = $this->getFromUsername($payload['username']);
        $current = new \DateTimeImmutable();

        if ($current->getTimestamp() - $user->getLastSeen()->getTimestamp() > 300) {
            $user->setLastSeen($current);

            $this->em->persist($user);
            $this->em->flush();
        }
    }

    public function getFromUsername(string $username): User
    {
        return $this->userRepo->findOneBy(['username' => $username]);
    }

    public function getUserFromRequest(Request $request): User
    {
        $authorizations = $request->headers->all('authorization')[0];
        $payload = $this->validateToken($authorizations);

        return $this->getFromUsername($payload['username']);
    }
}
