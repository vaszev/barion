<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionPaymentResponse;

/**
 * @method BarionPaymentResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionPaymentResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionPaymentResponse[]    findAll()
 * @method BarionPaymentResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionPaymentResponseRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionPaymentResponse::class);
  }
}
