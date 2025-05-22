<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserProperties(): void
    {
        $user = new User();
        $user->setUsername('alice');
        $user->setEmail('alice@example.com');
        $user->setPassword('supersecure');
        $user->setRoles(['ROLE_ADMIN']);

        self::assertEquals('alice', $user->getUsername());
        self::assertEquals('alice@example.com', $user->getEmail());
        self::assertEquals('supersecure', $user->getPassword());
        self::assertContains('ROLE_ADMIN', $user->getRoles());
        self::assertEquals('alice', $user->getUserIdentifier());
    }

    public function testDefaultRole(): void
    {
        $user = new User();
        self::assertContains('ROLE_USER', $user->getRoles());
    }
}
