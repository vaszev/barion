<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionPaymentTransaction;

/**
 * @method BarionPaymentTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionPaymentTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionPaymentTransaction[]    findAll()
 * @method BarionPaymentTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionPaymentTransactionRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionPaymentTransaction::class);
  }
}
