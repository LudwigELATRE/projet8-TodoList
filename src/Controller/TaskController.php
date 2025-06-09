<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    public function __construct(private readonly TaskRepository $taskRepository, private readonly Security $security)
    {
    }

    #[Route("/tasks", name: "task_list_start")]
    public function list(): Response
    {
        $tasks = $this->taskRepository->findBy([
            'user' => $this->getUser(),
            'isDone' => false,
        ]);
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route("manager/tasks", name: "task_list_anonyme")]
    #[IsGranted('ROLE_MANAGER')]
    public function listTaskAnonymeForManager(): Response
    {
        $tasks = $this->taskRepository->findBy([
            'user' => NULL,
            'isDone' => false,
        ]);

        return $this->render('task/anonyme_task.html.twig', ['tasks' => $tasks]);
    }

    #[Route("/tasks/end", name: "task_list_end")]
    public function listEnd(): Response
    {
        $tasks = $this->taskRepository->findBy(['isDone' => true]);
        return $this->render('task/list-end.html.twig', ['tasks' => $tasks]);
    }

    #[Route("/tasks/create", name: "task_create")]
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $task->setUser($user);
            $this->taskRepository->save($task);
            $this->addFlash('success', 'La tâche a bien été ajoutée.');
            return $this->redirectToRoute('task_list_start');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/tasks/{id}/edit", name: "task_edit")]
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskRepository->save($task);
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            return $this->redirectToRoute('task_list_start');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route("/tasks/{id}/toggle", name: "task_toggle")]
    public function toggleTask(Task $task, Request $request): Response
    {
        $task->toggle(!$task->isDone());
        $this->taskRepository->save($task);

        $message = $task->isDone()
            ? 'La tâche "%s" a bien été marquée comme faite.'
            : 'La tâche "%s" a bien été marquée comme non terminée.';

        $this->addFlash('success', sprintf($message, $task->getTitle()));

        // Redirige vers la page précédente
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('task_list_start'));
    }

    #[Route("/tasks/{id}/delete", name: "task_delete")]
    public function deleteTask(Task $task): Response
    {
        $this->taskRepository->remove($task);
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        return $this->redirectToRoute('task_list_start');
    }
}
