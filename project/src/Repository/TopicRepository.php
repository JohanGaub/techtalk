<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Topic;
use App\Enum\CurrentPlace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Topic>
 *
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security
    ) {
        parent::__construct($registry, Topic::class);
    }

    /**
     * Use leftJoin to retrieve ALL Topics, even those without an associated Meetup.
     * leftJoin improves performance by avoiding unnecessary joins.
     * If a Topic does not have an associated Meetup, the Meetup fields will be null.
     */
    public function getTopicsForBoardUser()
    {
        return $this->createQueryBuilder('t')
            ->select(
                't',
                'userPresenter',
                'meetup',
                'agency'
            )
            ->leftJoin('t.meetup', 'meetup')
            ->leftJoin('t.userPresenter', 'userPresenter')
            ->leftJoin('meetup.agency', 'agency')
            ->getQuery()
            ->getResult();
    }

    public function getTopicsForUser(): array
    {
        return $this->createQueryBuilder('t')
            ->select(
                't.id',
                't.label',
                't.description',
                't.duration',
                't.durationCategory',
                't.currentPlace',
                't.createdAt',
                't.updatedAt',
                'u.email as userPresenter', // Select the id of the userPresenter
                'userProposer.id as userProposerId', // Select the id of the userPresenter
                'm.label as meetupLabel',
                'm.startDate as meetupStartDate',
                'm.endDate as meetupEndDate',
                'a.label as meetupAgency'
            )
            ->leftJoin('t.meetup', 'm')
            ->leftJoin('t.userPresenter', 'u') // Retrieve the UserPresenter
            ->leftJoin('t.userProposer', 'userProposer') // Retrieve the UserProposer
            ->leftJoin('m.agency', 'a') // Retrieve the Agency from the Meetup
            // Only retrieve Topics with currentPlace = 'draft'
            ->andWhere('t.currentPlace = :currentPlace')
            // A user can only see their OWN topics
            ->andWhere('userProposer.id = :currentUserId')
            ->setParameter('currentPlace', CurrentPlace::DRAFT->value)
            ->setParameter('currentUserId', $this->security->getUser()->getId())
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Topic[] Returns an array of Topic objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Topic
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
