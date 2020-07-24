<?php

namespace App\Repository;

use App\Entity\Stripe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stripe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stripe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stripe[]    findAll()
 * @method Stripe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StripeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stripe::class);
    }

    // /**
    //  * @return Stripe[] Returns an array of Stripe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stripe
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
