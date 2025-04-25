<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
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

        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        self::assertResponseRedirects('/tasks');
    }

}