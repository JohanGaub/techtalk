<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MeetupUserParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MeetupUserParticipant>
 *
 * @method MeetupUserParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetupUserParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetupUserParticipant[]    findAll()
 * @method MeetupUserParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMeetupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetupUserParticipant::class);
    }

    //    /**
    //     * @return MeetupUserParticipant[] Returns an array of MeetupUserParticipant objects
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

    //    public function findOneBySomeField($value): ?MeetupUserParticipant
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
