<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionPaymentRequestModel;

/**
 * @method BarionPaymentRequestModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionPaymentRequestModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionPaymentRequestModel[]    findAll()
 * @method BarionPaymentRequestModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionPaymentRequestModelRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionPaymentRequestModel::class);
  }

  // /**
  //  * @return PaymentRequestModel[] Returns an array of PaymentRequestModel objects
  //  */
  /*
  public function findByExampleField($value)
  {
      return $this->createQueryBuilder('p')
          ->andWhere('p.exampleField = :val')
          ->setParameter('val', $value)
          ->orderBy('p.id', 'ASC')
          ->setMaxResults(10)
          ->getQuery()
          ->getResult()
      ;
  }
  */

  /*
  public function findOneBySomeField($value): ?PaymentRequestModel
  {
      return $this->createQueryBuilder('p')
          ->andWhere('p.exampleField = :val')
          ->setParameter('val', $value)
          ->getQuery()
          ->getOneOrNullResult()
      ;
  }
  */
}
