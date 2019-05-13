<?php

namespace Vaszev\BarionBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Barion {

  private $translator;
  private $container;
  private $em;



  public function __construct(TranslatorInterface $translator, ContainerInterface $container, EntityManagerInterface $em) {
    $this->translator = $translator;
    $this->container = $container;
    $this->em = $em;
  }

}