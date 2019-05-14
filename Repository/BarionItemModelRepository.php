<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionItemModel;

/**
 * @method BarionItemModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionItemModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionItemModel[]    findAll()
 * @method BarionItemModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionItemModelRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionItemModel::class);
  }

  // /**
  //  * @return BarionItemModel[] Returns an array of BarionItemModel objects
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
  public function findOneBySomeField($value): ?BarionItemModel
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
