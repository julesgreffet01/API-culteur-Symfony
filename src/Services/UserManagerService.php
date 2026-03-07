<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserManagerService
{
    private UserRepository $userRepository;
    public function __construct(UserRepository $repository)
    {
        $this->userRepository = $repository;
    }

    public function getUsersPaginate(int $page): Paginator{
        return $this->userRepository->getUsersPagination($page);
    }

    public function getUserById(int $id): User{
        return $this->userRepository->find($id);
    }
}
