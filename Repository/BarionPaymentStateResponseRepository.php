<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionPaymentStateResponse;

/**
 * @method BarionPaymentStateResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionPaymentStateResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionPaymentStateResponse[]    findAll()
 * @method BarionPaymentStateResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionPaymentStateResponseRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionPaymentStateResponse::class);
  }
}
