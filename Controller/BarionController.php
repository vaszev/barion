<?php

namespace Vaszev\BarionBundle\Controller;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vaszev\BarionBundle\Service\Barion;

class BarionController extends AbstractController {

  static function getLocale($default = 'hu') {
    if (isset($GLOBALS['request']) && $GLOBALS['request']) {
      $locale = strtolower($GLOBALS['request']->getLocale());

      return $locale;
    }


    return $default;
  }


  public function test(Barion $barion) {
    $redirectURL = $this->generateUrl('index',[],UrlGeneratorInterface::ABSOLUTE_URL);
    try {
      $barion->init('http://asdasd.asd', '')
             ->createTransactionModel('komment');

      for ($i = 1; $i <= 5; $i++) {
        $barion->addItem('terméknév' . $i,'leírás', $i, 100 * $i, "skuNumber" . $i);
      }
      $barion->preparePaymentRequestModel('user@example@com', '1234 Cím, utca hsz.')
             ->send();

    } catch (\Exception $e) {
      dump($e);
    }

  }

  /**
   * @param Request $request
   * @param LoggerInterface $logger
   * @return array
   * @Route("/callback", name="barion_callback")
   * @Template("@VaszevBarion/barion/callback.html.twig")
   */
  public function callback(Request $request, LoggerInterface $logger) {
    $ret = [];

    $r = $request->request->all();
    $q = $request->query->all();
    $logger->critical(serialize($r));
    $logger->critical(serialize($q));

    return $ret;
  }

}