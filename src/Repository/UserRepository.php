<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{

    public function __construct(ManagerRegistry $registry, private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getUsersPagination(int $page){
        return new Paginator($this->createQueryBuilder('u')
            ->setMaxResults(10)
            ->setFirstResult(($page - 1) * 10)
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            false
        );
    }

    public function save(User $user, bool $flush = true){
        $this->getEntityManager()->persist($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(User $user, bool $flush = true){
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }

    public function authenticateUserForJwt(string $email, string $password): User
    {
        $user = $this->findOneBy(['email' => $email]);
        if (!$user) {
            //timing attack
            $fakeUser = new User();
            $fakeUser->setPassword('$2y$13$C8wS0QJ3R1hQ0mY8n2L6eO0mYxgK0n8R4sYV6vXz2OQm7QmGm6V2K');
            $this->passwordHasher->isPasswordValid($fakeUser, $password);
            throw new BadCredentialsException();
        }
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new BadCredentialsException();
        }
        return $user;
    }
}
