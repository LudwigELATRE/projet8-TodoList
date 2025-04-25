<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testCreateTaskAsAuthenticatedUser(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@example.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/tasks/create');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Tâche test',
            'task[content]' => 'Contenu test',
        ]);
        $client->submit($form);
        self::assertResponseRedirects('/tasks');
        $client->followRedirect();
    }

    public function testToggleTask(): void
    {
        $client = static::createClient();

        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $entityManager = $container->get('doctrine')->getManager();

        $user = $userRepository->findOneBy(['email' => 'user@example.com']);

        $task = new Task();
        $task->setTitle('Tâche à basculer');
        $task->setContent('Contenu');
        $task->setCreatedAt(new \DateTime());
        $task->setUser($user);

        $entityManager->persist($task);
        $entityManager->flush();

        $client->loginUser($user);

        $client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        self::assertResponseRedirects('/tasks'); // ou autre route attendue
    }

    public function testDeleteTask(): void
    {
        $client = static::createClient();

        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $entityManager = $container->get('doctrine')->getManager();

        $user = $userRepository->findOneBy(['email' => 'user@example.com']);

        $task = new Task();
        $task->setTitle('Tâche à supprimer');
        $task->setContent('Contenu de la tâche');
        $task->setCreatedAt(new \DateTime());
        $task->setUser($user);

        $entityManager->persist($task);
        $entityManager->flush();

        $client->loginUser($user);

        $client->request('POST', '/tasks/' . $task->getId() . '/delete');

        self::assertResponseRedirects('/tasks');
    }

    public function testListEnd(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $userRepo = $container->get(UserRepository::class);

        $user = $userRepo->findOneBy(['email' => 'user@example.com']);
        $client->loginUser($user);

        $task = new Task();
        $task->setTitle('Tâche terminée');
        $task->setContent('Contenu de la tâche');
        $task->setCreatedAt(new \DateTime());
        $task->toggle(true);
        $task->setUser($user);
        $entityManager->persist($task);
        $entityManager->flush();

        $crawler = $client->request('GET', '/tasks/end');

        self::assertResponseIsSuccessful(); // HTTP 200
        self::assertSelectorTextContains('body', 'Tâche terminée'); // le titre doit apparaître
    }

    public function testEditTask(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $userRepo = $container->get(\App\Repository\UserRepository::class);

        // Connexion d'un utilisateur existant
        $user = $userRepo->findOneBy(['email' => 'user@example.com']);
        $client->loginUser($user);

        // Création d'une tâche
        $task = new \App\Entity\Task();
        $task->setTitle('Tâche originale');
        $task->setContent('Contenu original');
        $task->setUser($user);
        $em->persist($task);
        $em->flush();

        // Accès au formulaire d’édition
        $crawler = $client->request('GET', '/tasks/' . $task->getId() . '/edit');
        self::assertResponseIsSuccessful();

        // Soumission du formulaire avec les données modifiées
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Tâche modifiée',
            'task[content]' => 'Nouveau contenu',
        ]);
        $client->submit($form);

        // Vérification de la redirection
        self::assertResponseRedirects('/tasks');
        $client->followRedirect();
    }

}