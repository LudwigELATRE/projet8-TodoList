<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @codeCoverageIgnore
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $task, bool $flush = true): void
    {
        $this->getEntityManager()->persist($task);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $task, bool $flush = true): void
    {
        $this->getEntityManager()->remove($task);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countTasksByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(CASE WHEN t.isDone = true THEN 1 ELSE 0 END) AS doneCount')
            ->addSelect('SUM(CASE WHEN t.isDone = false THEN 1 ELSE 0 END) AS notDoneCount')
            ->where('t.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return Task[] Returns an array of incomplete tasks
     */
/*    public function findIncompleteTasks(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isDone = :done')
            ->setParameter('done', false)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }*/
}