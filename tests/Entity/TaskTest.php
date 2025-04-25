<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskProperties(): void
    {
        $user = new User();
        $user->setUsername('john');
        $user->setEmail('john@example.com');
        $user->setPassword('secret');

        $task = new Task();
        $task->setTitle('Titre de test');
        $task->setContent('Contenu de test');
        $task->setUser($user);

        $this->assertEquals('Titre de test', $task->getTitle());
        $this->assertEquals('Contenu de test', $task->getContent());
        $this->assertSame($user, $task->getUser());
        $this->assertFalse($task->isDone());

        $task->toggle(true);
        self::assertTrue($task->isDone());
    }
}


