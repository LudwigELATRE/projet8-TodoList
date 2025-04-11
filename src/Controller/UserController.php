<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    #[Route('/users/list', name: 'user_list')]
    public function listAction(): Response
    {
        $users = $this->userRepository->findAll();

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

    #[Route('/users/{id}/edit', name: 'user_edit')]
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

            return $this->redirectToRoute('user_list');
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

        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function deleteAction(Request $request, User $user): Response
    {
        // Protection CSRF
        if ($this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user);
            $this->addFlash('success', "L'utilisateur a bien été supprimé.");
        }

        return $this->redirectToRoute('user_list');
    }
}
