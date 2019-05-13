<?php

namespace Vaszev\BarionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  private $posKey = null;



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
        ->end();

    return $builder;
  }
}
