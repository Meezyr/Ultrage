<?php

namespace App\Repository;

use App\Entity\Documentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentation>
 *
 * @method Documentation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentation[]    findAll()
 * @method Documentation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentation::class);
    }

    public function add(Documentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Documentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllByCriteria($conditions): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('d')
            ->from('App\Entity\Documentation', 'd')
            ->where('d.publish = TRUE')
            ->setMaxResults(null)
        ;

        if (!empty($conditions['search'])) {
            $qb->andWhere($qb->expr()->like('d.title', ':sea'))
                ->orWhere($qb->expr()->like('d.excerpt', ':sea'))
                ->setParameter('sea', '%' . $conditions['search'] . '%');
        }

        if (!empty($conditions['category'])) {
            $qb->andWhere('JSON_CONTAINS(d.category, :cat) = 1')
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

            $qb->orderBy('d.'.$orderBy[0], $orderBy[1]);
        } else {
            $qb->orderBy('d.release_date', 'ASC');
        }

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findFourOrderByDate($order): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.release_date', $order)
            ->where('d.publish = TRUE')
            ->setMaxResults(4)
            ->getQuery()
            ->execute()
            ;
    }

//    /**
//     * @return Documentation[] Returns an array of Documentation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Documentation
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
