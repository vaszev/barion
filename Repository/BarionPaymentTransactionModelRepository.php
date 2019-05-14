<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionPaymentTransactionModel;

/**
 * @method BarionPaymentTransactionModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionPaymentTransactionModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionPaymentTransactionModel[]    findAll()
 * @method BarionPaymentTransactionModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionPaymentTransactionModelRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionPaymentTransactionModel::class);
  }

  // /**
  //  * @return BarionPaymentTransactionModel[] Returns an array of BarionPaymentTransactionModel objects
  //  */
  /*
  public function findByExampleField($value)
  {
      return $this->createQueryBuilder('b')
          ->andWhere('b.exampleField = :val')
          ->setParameter('val', $value)
          ->orderBy('b.id', 'ASC')
          ->setMaxResults(10)
          ->getQuery()
          ->getResult()
      ;
  }
  */

  /*
  public function findOneBySomeField($value): ?BarionPaymentTransactionModel
  {
      return $this->createQueryBuilder('b')
          ->andWhere('b.exampleField = :val')
          ->setParameter('val', $value)
          ->getQuery()
          ->getOneOrNullResult()
      ;
  }
  */
}
