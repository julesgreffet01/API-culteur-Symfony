<?php

namespace App\Controller;

use App\Services\UserManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $maxPage = ceil(count($users) / 20);
        return $this->render('user_manager/index.html.twig', [
            'controller_name' => 'UserManagerController',
            'users' => $users,
            'maxPage' => $maxPage,
            'currentPage' => $page,
        ]);
    }

    #[Route('/users/update/{id}', name: 'app_user_update',  methods: ['POST', 'GET'])]
    public function edit(int $id): Response{
        $user = $this->userManagerService->getUserById($id);

    }

    #[Route('/users/create', name: 'app_user_create', methods: ['POST', 'GET'])]
    public function create(Request $request): Response{

    }
}
