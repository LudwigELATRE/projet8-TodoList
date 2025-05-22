<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRoleType;
use App\Form\UserType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private AuthorizationCheckerInterface $authorizationChecker,
        private readonly TaskRepository $taskRepository
    ) {}

    #[Route('admin/users/list', name: 'user_list_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('manager/users/list', name: 'user_list_manager')]
    #[IsGranted('ROLE_MANAGER')]
    public function listUserForManager(): Response
    {
        $users = $this->userRepository->listUsersWithUserAndManagerRoles();

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/user/create', name: 'user_create')]
    public function createAction(Request $request): Response
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('default');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
            }

            $this->userRepository->save($user);
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('login');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/{id}/edit', name: 'user_edit')]
    public function editAction(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
            }

            $this->userRepository->save($user);
            $this->addFlash('success', "L'utilisateur a bien été modifié.");

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/user/profile', name: 'user_profile')]
    public function profileAction(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $taskStats = $this->taskRepository->countTasksByUser($user);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'taskStats' => $taskStats
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'user_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteAction(Request $request, User $user): Response
    {
        // Protection CSRF
        if ($this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user);
            $this->addFlash('success', "L'utilisateur a bien été supprimé.");
        }

        return $this->redirectToRoute('user_list');
    }

    #[Route('/admin/users/{id}/edit-role', name: 'user_update_role')]
    #[IsGranted('ROLE_ADMIN')]
    public function editRoleForm(Request $request, User $user): Response
    {
        // Empêche un utilisateur de modifier son propre rôle
        if ($this->getUser()?->getId() === $user->getId()) {
            $this->addFlash('warning', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('user_list');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $availableRoles = [
                'Administrateur' =>'ROLE_ADMIN',
                'Utilisateur' => 'ROLE_USER',
                'Manager' => 'ROLE_MANAGER'];
        } elseif ($this->isGranted('ROLE_MANAGER')) {
            $availableRoles = [
                'Utilisateur' => 'ROLE_USER',
                'Manager' => 'ROLE_MANAGER'];
        } else {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $form = $this->createForm(UserRoleType::class, $user, [
            'available_roles' => $availableRoles,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedRole = ($form->get('roles')->getData());

            $user->setRoles($selectedRole);

            $this->userRepository->save($user);
            $this->addFlash('success', 'Le rôle a bien été mis à jour.');

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/update_role.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'available_roles' => ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_CUSTOMER'],
        ]);
    }

}
