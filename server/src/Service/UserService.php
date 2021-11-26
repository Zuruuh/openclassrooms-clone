<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    public const USER_DOES_NOT_EXIST = 'This user does not exists';

    public function __construct(
        UserRepository $userRepo,
    ) {
        $this->userRepo = $userRepo;
    }


    public function exists(array $params = []): User
    {
        $user = $this->userRepo->findOneBy($params);
        if (!$user) {
            throw new NotFoundHttpException(self::USER_DOES_NOT_EXIST);
        }

        return $user;
    }
}
