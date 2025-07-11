<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class TaskServiceTest extends TestCase
{
    public function testCreate(): void
    {
        $user = $this->createMock(User::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $task = $this->createMock(Task::class);
        $task->expects($this->once())->method('setUser')->with($user);

        $repo = $this->createMock(TaskRepository::class);
        $repo->expects($this->once())->method('save')->with($task);

        $service = new TaskService($repo, $security);
        $service->create($task);
    }

    public function testEdit(): void
    {
        $task = $this->createMock(Task::class);

        $repo = $this->createMock(TaskRepository::class);
        $repo->expects($this->once())->method('save')->with($task);

        $security = $this->createMock(Security::class);

        $service = new TaskService($repo, $security);
        $service->edit($task);
    }

    public function testDelete(): void
    {
        $task = $this->createMock(Task::class);

        $repo = $this->createMock(TaskRepository::class);
        $repo->expects($this->once())->method('remove')->with($task);

        $security = $this->createMock(Security::class);

        $service = new TaskService($repo, $security);
        $service->delete($task);
    }

    public function testToggleToDone(): void
    {
        $task = $this->createMock(Task::class);
        $task->expects($this->exactly(2))
            ->method('isDone')
            ->willReturnOnConsecutiveCalls(false, true);

        $task->expects($this->once())
            ->method('toggle')
            ->with(true);

        $task->expects($this->once())
            ->method('getTitle')
            ->willReturn('Tâche test');

        $repo = $this->createMock(TaskRepository::class);
        $repo->expects($this->once())->method('save')->with($task);

        $security = $this->createMock(Security::class);

        $service = new TaskService($repo, $security);
        $message = $service->toggle($task);

        $this->assertSame('La tâche "Tâche test" a bien été marquée comme faite.', $message);
    }

    public function testToggleToUndone(): void
    {
        $task = $this->createMock(Task::class);
        $task->expects($this->exactly(2))
            ->method('isDone')
            ->willReturnOnConsecutiveCalls(true, false);

        $task->expects($this->once())
            ->method('toggle')
            ->with(false);

        $task->expects($this->once())
            ->method('getTitle')
            ->willReturn('Tâche test');

        $repo = $this->createMock(TaskRepository::class);
        $repo->expects($this->once())->method('save')->with($task);

        $security = $this->createMock(Security::class);

        $service = new TaskService($repo, $security);
        $message = $service->toggle($task);

        $this->assertSame('La tâche "Tâche test" a bien été marquée comme non terminée.', $message);
    }
}