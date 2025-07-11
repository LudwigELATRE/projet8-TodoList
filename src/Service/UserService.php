<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function create(User $user): void
    {
        if ($user->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }

        $this->userRepository->save($user);
    }

    public function update(User $user): void
    {
        if ($user->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }

        $this->userRepository->save($user);
    }

    public function delete(User $user): void
    {
        $this->userRepository->remove($user);
    }

    public function updateRole(User $user, array $roles): void
    {
        $user->setRoles($roles);
        $this->userRepository->save($user);
    }
}
