<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserListPage(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($user);

        $client->request('GET', '/users/list');
        self::assertResponseIsSuccessful();
    }

    public function testCreateUserForm(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/create');
        self::assertResponseIsSuccessful();
    }

    public function testProfileRedirectsIfNotLogged(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/profile');
        self::assertResponseRedirects('/login');
    }
}