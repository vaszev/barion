<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionPaymentResponseModel;

/**
 * @method BarionPaymentResponseModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionPaymentResponseModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionPaymentResponseModel[]    findAll()
 * @method BarionPaymentResponseModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionPaymentResponseModelRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionPaymentResponseModel::class);
  }

  // /**
  //  * @return BarionPaymentResponseModel[] Returns an array of BarionPaymentResponseModel objects
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
  public function findOneBySomeField($value): ?BarionPaymentResponseModel
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
