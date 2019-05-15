<?php

namespace Vaszev\BarionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  private $sandbox = true;
  private $apiVersion = 2;
  private $webshopName = 'wsm';
  private $waitingRoomBg = '#fff';
  private $waitingRoomColor = '#333';
  private $waitingRoomAmountColor = 'tomato';
  private $waitingRoomPositiveFeedbackColor = 'green';
  private $waitingRoomNegativeFeedbackColor = 'red';
  private $waitingRoomNeturalFeedbackColor = 'orange';



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
        ->variableNode('pixelId')->isRequired()->cannotBeEmpty()->end()
        ->variableNode('webshopName')->defaultValue($this->webshopName)->end()
        ->variableNode('webshopDefaultRoute')->isRequired()->cannotBeEmpty()->end()
        ->variableNode('waitingRoomBg')->defaultValue($this->waitingRoomBg)->end()
        ->variableNode('waitingRoomColor')->defaultValue($this->waitingRoomColor)->end()
        ->variableNode('waitingRoomAmountColor')->defaultValue($this->waitingRoomAmountColor)->end()
        ->variableNode('waitingRoomPositiveFeedbackColor')->defaultValue($this->waitingRoomPositiveFeedbackColor)->end()
        ->variableNode('waitingRoomNegativeFeedbackColor')->defaultValue($this->waitingRoomNegativeFeedbackColor)->end()
        ->variableNode('waitingRoomNeturalFeedbackColor')->defaultValue($this->waitingRoomNeturalFeedbackColor)->end()
        ->end();

    return $builder;
  }
}
