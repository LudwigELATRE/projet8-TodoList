<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\TaskService;
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
    public function listTaskAnonymeForManager(Security $security): Response
    {
        if (!$security->isGranted('ROLE_MANAGER') && !$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

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
    public function create(Request $request, TaskService $taskService): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskService->create($task);
            $this->addFlash('success', 'La tâche a bien été ajoutée.');
            return $this->redirectToRoute('task_list_start');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/tasks/{id}/edit", name: "task_edit")]
    public function edit(Task $task, Request $request, TaskService $taskService): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskService->edit($task);
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            return $this->redirectToRoute('task_list_start');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route("/tasks/{id}/toggle", name: "task_toggle")]
    public function toggleTask(Task $task, Request $request, TaskService $taskService): Response
    {
        $message = $taskService->toggle($task);
        $this->addFlash('success', sprintf($message, $task->getTitle()));

        // Redirige vers la page précédente
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('task_list_start'));
    }

    #[Route("/tasks/{id}/delete", name: "task_delete")]
    public function deleteTask(Task $task, TaskService $taskService): Response
    {
        $taskService->delete($task);
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        return $this->redirectToRoute('task_list_start');
    }
}
