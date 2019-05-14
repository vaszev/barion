<?php

namespace Vaszev\BarionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  private $sandbox = true;
  private $apiVersion = 2;
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
        ->variableNode('posKey')->isRequired()->cannotBeEmpty()->end()
        ->booleanNode('sandbox')->defaultValue($this->sandbox)->end()
        ->integerNode('apiVersion')->defaultValue($this->apiVersion)->end()
        ->variableNode('payee')->isRequired()->cannotBeEmpty()->end()
        ->variableNode('webshopName')->defaultValue($this->webshopName)->end()
        ->end();

    return $builder;
  }
}
