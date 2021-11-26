<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwtService
{
    private JWTTokenManagerInterface $jwtManager;
    private UserRepository           $userRepo;

    public const INVALID_TOKEN = 'This action needs a valid token!';

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserRepository           $userRepo
    ) {
        $this->jwtManager = $jwtManager;
        $this->userRepo   = $userRepo;
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
            $client = $this->jwtManager->parse(substr($token, 7));

            return $client;
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
}
