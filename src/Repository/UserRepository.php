<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user, bool $flush = true): void
    {
        $this->getEntityManager()->persist($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function listUsersWithUserAndManagerRoles(): array
    {
        $users = [];
        $userWithRoleUser = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roleUser')
            ->setParameter('roleUser', '%ROLE_USER%')
            ->getQuery()
            ->getResult();

        $managerWithRoleManager = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roleManager')
            ->setParameter('roleManager', '%ROLE_MANAGER%')
            ->getQuery()
            ->getResult();

        foreach ($managerWithRoleManager as $user) {
            $users[] = $user;
        }

        foreach ($userWithRoleUser as $user) {
            $users[] = $user;
        }


        return $users;
    }


    public function remove(User $user, bool $flush = true): void
    {
        $this->getEntityManager()->remove($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a user by their email.
     */
/*    public function findOneByEmail(string $email): ?UserFixtures
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }*/
}