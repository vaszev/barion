<?php

namespace Vaszev\BarionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaszev\BarionBundle\Entity\BarionItem;

/**
 * @method BarionItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarionItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarionItem[]    findAll()
 * @method BarionItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarionItemRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, BarionItem::class);
  }
}
