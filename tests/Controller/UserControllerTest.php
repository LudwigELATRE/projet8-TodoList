<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        $client->request('POST', '/user/create');
        self::assertResponseIsSuccessful();
    }

    public function testProfileRedirectsIfNotLogged(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/profile');
        self::assertResponseRedirects('/login');
    }
    public function testCreateUserValidSubmission(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user/create');
        $form = $crawler->selectButton('Créer')->form([
            'user[username]' => 'newusertestcreate',
            'user[email]' => 'newusertestcreate@example.com',
            'user[password][first]' => 'Password123!',
            'user[password][second]' => 'Password123!',
        ]);
        $client->submit($form);

        self::assertResponseRedirects('/login');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testEditUser(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneBy(['email' => 'newusertestcreate@example.com']);
        $client->loginUser($user);

        $crawler = $client->request('PUT', '/users/' . $user->getId() . '/edit');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'newusertestcreate2',
            'user[email]' => 'newusertestcreate@example.com',
            'user[password][first]' => 'Password123!',
            'user[password][second]' => 'Password123!',
        ]);
        $client->submit($form);

        self::assertResponseRedirects('/users/list');
        $client->followRedirect();
    }

    public function testProfileAsAuthenticatedUser(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($user);

        $client->request('GET', '/user/profile');
        self::assertResponseIsSuccessful();
    }

/*    public function testDeleteUserWithValidCsrfToken(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $userRepo = $container->get(UserRepository::class);
        $em = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // Connexion admin
        $admin = $userRepo->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($admin);

        // Création de l'utilisateur cible
        $userToDelete = new User();
        $userToDelete->setEmail('delete@example.com');
        $userToDelete->setUsername('delete_user');
        $userToDelete->setRoles(['ROLE_USER']);
        $userToDelete->setPassword(
            $passwordHasher->hashPassword($userToDelete, 'Password123!')
        );
        $em->persist($userToDelete);
        $em->flush();

        // 🔥 Force le démarrage explicite de la session AVANT de générer le token
        $session = $container->get('session');
        $session->start();

        // ✅ Génère le token maintenant que la session est prête
        $csrfToken = $container->get('security.csrf.token_manager')
            ->getToken('delete_user_' . $userToDelete->getId());

        // Requête de suppression
        $client->request('POST', '/users/' . $userToDelete->getId() . '/delete', [
            '_token' => $csrfToken,
        ]);

        self::assertResponseRedirects('/users/list');
        $client->followRedirect();
        self::assertSelectorExists('.flash-success');
    }*/



/*    public function testDeleteUserWithInvalidCsrfToken(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($user);

        $em = static::getContainer()->get('doctrine')->getManager();
        $userToDelete = clone $user;
        $userToDelete->setEmail('invalid-delete@example.com');
        $em->persist($userToDelete);
        $em->flush();

        $client->request('POST', '/users/' . $userToDelete->getId() . '/delete', [
            '_token' => 'invalid-token',
        ]);

        self::assertResponseRedirects('/users/list');
        $client->followRedirect();
        self::assertSelectorNotExists('.flash-success');
    }*/

}