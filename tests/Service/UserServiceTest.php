<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    public function testCreateWithPassword(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->exactly(2))
            ->method('getPassword')
            ->willReturn('plainPassword');

        $user->expects($this->once())
            ->method('setPassword')
            ->with('hashedPassword');

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'plainPassword')
            ->willReturn('hashedPassword');

        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())->method('save')->with($user);

        $service = new UserService($repo, $hasher);
        $service->create($user);
    }

    public function testUpdateWithPassword(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->exactly(2))
            ->method('getPassword')
            ->willReturn('plainPassword');

        $user->expects($this->once())
            ->method('setPassword')
            ->with('hashedPassword');

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'plainPassword')
            ->willReturn('hashedPassword');

        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())
            ->method('save')->with($user);

        $service = new UserService($repo, $hasher);
        $service->update($user);
    }


    public function testDelete(): void
    {
        $user = $this->createMock(User::class);

        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())
            ->method('remove')
            ->with($user);

        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $service = new UserService($repo, $hasher);
        $service->delete($user);
    }

    public function testUpdateRole(): void
    {
        $user = $this->createMock(User::class);
        $roles = ['ROLE_MANAGER'];

        $user->expects($this->once())
            ->method('setRoles')
            ->with($roles);

        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())
            ->method('save')
            ->with($user);

        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $service = new UserService($repo, $hasher);
        $service->updateRole($user, $roles);
    }
}