<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionPaymentRequest;

/**
 * @method BarionPaymentRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionPaymentRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionPaymentRequest[]    findAll()
 * @method BarionPaymentRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionPaymentRequestRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionPaymentRequest::class);
  }
}
