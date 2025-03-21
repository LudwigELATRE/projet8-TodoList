<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class TaskController extends AbstractController
{


    public function __construct(private readonly TaskRepository $taskRepository)
    {
    }

    /**
     * Affiche la liste des tâches.
     *
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/tasks", name: "task_list")]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $tasks = $this->taskRepository->findAll();
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    /**
     * Crée une nouvelle tâche.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/tasks/create", name: "task_create")]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskRepository->save($task);

            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Modifie une tâche existante.
     *
     * @param Task $task
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/tasks/{id}/edit", name: "task_edit")]
    public function edit(Task $task, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * Bascule l’état (faite / non faite) d’une tâche.
     *
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/tasks/{id}/toggle", name: "task_toggle")]
    public function toggleTask(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->toggle(!$task->isDone());
        $entityManager->flush();
        if ($task->isDone() === true) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        }

        return $this->redirectToRoute('task_list');
    }

    /**
     * Supprime une tâche.
     *
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/tasks/{id}/delete", name: "task_delete")]
    public function deleteTask(Task $task, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}

