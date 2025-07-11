<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly Security $security
    ) {}

    public function create(Task $task): void
    {
        $task->setUser($this->security->getUser());
        $this->taskRepository->save($task);
    }

    public function edit(Task $task): void
    {
        $this->taskRepository->save($task);
    }

    public function toggle(Task $task): string
    {
        $task->toggle(!$task->isDone());
        $this->taskRepository->save($task);

        return $task->isDone()
            ? sprintf('La tâche "%s" a bien été marquée comme faite.', $task->getTitle())
            : sprintf('La tâche "%s" a bien été marquée comme non terminée.', $task->getTitle());
    }

    public function delete(Task $task): void
    {
        $this->taskRepository->remove($task);
    }
}
