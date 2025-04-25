<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for ($i = 1; $i <= 3; $i++) {
            $task = new Task();
            $task->setTitle("Tâche $i");
            $task->setContent("Contenu de la tâche $i");
            $task->setUser($user);
            $manager->persist($task);
        }

        $taskAdmin = new Task();
        $taskAdmin->setTitle("Tâche admin");
        $taskAdmin->setContent("Contenu réservé à l'admin");
        $taskAdmin->setUser($admin);
        $manager->persist($taskAdmin);

        $manager->flush();
    }
}
