<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserTopicVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserTopicVote>
 *
 * @method UserTopicVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTopicVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTopicVote[]    findAll()
 * @method UserTopicVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTopicVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTopicVote::class);
    }

    //    /**
    //     * @return UserTopicVote[] Returns an array of UserTopicVote objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserTopicVote
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
