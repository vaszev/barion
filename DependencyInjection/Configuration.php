<?php

namespace Vaszev\BarionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  private $posKey = null;
  private $sandbox = true;
  private $apiVersion = 2;
  private $payee = null;
  private $webshopName = 'wsm';



  /**
   * Generates the configuration tree builder.
   * @return TreeBuilder $builder The tree builder
   */
  public function getConfigTreeBuilder() {
    $builder = new TreeBuilder();
    $rootNode = $builder->root('vaszev_barion');
    $rootNode
        ->children()
        ->variableNode('posKey')->defaultValue($this->posKey)->end()
        ->booleanNode('sandbox')->defaultValue($this->sandbox)->end()
        ->integerNode('apiVersion')->defaultValue($this->apiVersion)->end()
        ->variableNode('payee')->defaultValue($this->payee)->end()
        ->variableNode('webshopName')->defaultValue($this->webshopName)->end()
        ->end();

    return $builder;
  }
}
