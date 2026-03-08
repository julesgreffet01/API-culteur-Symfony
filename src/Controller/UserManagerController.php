<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Services\UserManagerService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserManagerController extends AbstractController
{

    private UserManagerService $userManagerService;

    public function __construct(UserManagerService $userManagerService)
    {
        $this->userManagerService = $userManagerService;
    }

    #[Route('/users', name: 'app_user_list', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $users = $this->userManagerService->getUsersPaginate($page);
        $maxPage = ceil(count($users) / 10);
        return $this->render('user_manager/index.html.twig', [
            'controller_name' => 'UserManagerController',
            'users' => $users,
            'maxPage' => $maxPage,
            'currentPage' => $page,
        ]);
    }

    #[Route('/users/update/{id}', name: 'app_user_update',  methods: ['POST', 'GET'])]
    public function edit(int $id, Request $request): Response{
        $user = $this->userManagerService->getUserById($id);
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userManagerService->update($user);
                $this->addFlash('success', 'Utilisateur '.$user->getName().' modifié');
                return $this->redirectToRoute('app_user_list');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('username')->addError(
                    new FormError("Ce nom d'utilisateur existe déjà")
                );
            }
        }
        return $this->render('user_manager/form.html.twig', [
            'form' => $form,
            'action' => 'update',
            'user' => $user,
        ]);
    }

    #[Route('/users/create', name: 'app_user_create', methods: ['POST', 'GET'])]
    public function create(Request $request): Response{
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userManagerService->create($user);
                $this->addFlash('success', 'Utilisateur créer');
                return $this->redirectToRoute('app_user_list');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('username')->addError(
                    new FormError("Ce nom d'utilisateur existe déjà")
                );
            }
        }
        return $this->render('user_manager/form.html.twig', [
            'form' => $form,
            'action' => 'create',
        ]);
    }

    #[Route('/users/delete/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(int $id): Response{
        $this->userManagerService->delete($id);
        $this->addFlash('success', 'Utilisateur supprimé');
        return $this->redirectToRoute('app_user_list');
    }
}
