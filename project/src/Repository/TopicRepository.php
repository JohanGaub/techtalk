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
            ->select('t') // Select the entire Topic object
            ->leftJoin('t.meetup', 'meetup')
            ->leftJoin('t.userPresenter', 'userPresenter')
            ->leftJoin('t.userProposer', 'userProposer')
            ->leftJoin('meetup.agency', 'agency')
            // Only retrieve Topics with currentPlace = 'draft'
            ->andWhere('t.currentPlace = :currentPlace')
            // A user can only see their OWN topics
            ->andWhere('userProposer.id = :currentUserId')
            ->setParameter('currentPlace', CurrentPlace::DRAFT->value)
            ->setParameter('currentUserId', $this->security->getUser()->getId())
            ->getQuery()
            ->getResult();
    }
}
