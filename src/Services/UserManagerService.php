<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserManagerService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function getUsersPaginate(int $page): Paginator
    {
        return $this->userRepository->getUsersPagination($page);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function create(User $user): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user);
    }

    public function update(User $user): void {
        $this->userRepository->save($user);
    }

    public function delete(int $userId) {
        $user = $this->userRepository->find($userId);
        if($user) {
            $this->userRepository->delete($user);
        } else {
            throw new UserNotFoundException();
        }
    }
}
