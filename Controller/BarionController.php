<?php

namespace Vaszev\BarionBundle\Controller;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BarionController extends AbstractController {

  static function getLocale($default = 'hu') {
    if (isset($GLOBALS['request']) && $GLOBALS['request']) {
      $locale = strtolower($GLOBALS['request']->getLocale());

      return $locale;
    }

    return $default;
  }



  /**
   * @param Request $request
   * @param LoggerInterface $logger
   * @return array
   * @Route("/callback/{id}", defaults={"id"=null}, name="barion_callback")
   * @Template("@VaszevBarion/barion/callback.html.twig")
   */
  public function callback(Request $request, LoggerInterface $logger) {
    $ret = [];

    $r = $request->request->all();
    $q = $request->query->all();
    $logger->debug(serialize($r));
    $logger->debug(serialize($q));


    return $ret;
  }

}