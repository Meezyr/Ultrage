<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Link>
 *
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function add(Link $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Link $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllByCriteria($conditions): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('l')
            ->from('App\Entity\Link', 'l')
            ->setMaxResults(null)
        ;

        if (!empty($conditions['search'])) {
            $qb->andWhere($qb->expr()->like('l.title', ':sea'))
                ->setParameter('sea', '%' . $conditions['search'] . '%');
        }

        if (!empty($conditions['category'])) {
            $qb->andWhere('JSON_CONTAINS(l.categories, :cat) = 1')
                ->setParameter('cat', json_encode($conditions['category']));
        }

        if (!empty($conditions['order'])) {
            $orderBy = match ($conditions['order']) {
                'release_date_ancient' => ['release_date', 'ASC'],
                'update_date_recent' => ['update_date', 'DESC'],
                'update_date_ancient' => ['update_date', 'ASC'],
                'title_increase' => ['title', 'ASC'],
                'title_decrease' => ['title', 'DESC'],
                default => ['release_date', 'DESC'],
            };

            $qb->orderBy('l.'.$orderBy[0], $orderBy[1]);
        } else {
            $qb->orderBy('l.release_date', 'ASC');
        }

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findFourOrderByDate($order): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.release_date', $order)
            ->setMaxResults(4)
            ->getQuery()
            ->execute()
            ;
    }

    //    /**
    //     * @return Link[] Returns an array of Link objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Link
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
