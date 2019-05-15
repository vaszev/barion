<?php

namespace Vaszev\BarionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vaszev\BarionBundle\Entity\BarionPaymentResponse;
use Vaszev\BarionBundle\Service\Barion;

class BarionController extends AbstractController {

  const WAITING_ROOM_TRY = 60;



  static function getLocale($default = 'hu') {
    if (isset($GLOBALS['request']) && $GLOBALS['request']) {
      $locale = strtolower($GLOBALS['request']->getLocale());

      return $locale;
    }

    return $default;
  }



  /**
   * @param Request $request
   * @param Barion $barion
   * @param null $paymentId
   * @param int $tries
   * @return array
   * @Route("/waiting-room", name="bairon_waiting_room")
   * @Route("/waiting-room/{paymentId}/{tries}", defaults={"paymentId"=null,"tries"=60}, requirements={"tries"="\d+"}, name="bairon_waiting_room_check")
   * @Template("@VaszevBarion/barion/waitingRoom.html.twig")
   */
  public function waitingRoom(Request $request, Barion $barion, $paymentId = null, $tries = 60) {
    $ret = [
        'webshopDefaultRoute'              => $this->getParameter('vaszev_barion.webshopDefaultRoute'),
        'waitingRoomBg'                    => $this->getParameter('vaszev_barion.waitingRoomBg'),
        'waitingRoomColor'                 => $this->getParameter('vaszev_barion.waitingRoomColor'),
        'waitingRoomAmountColor'           => $this->getParameter('vaszev_barion.waitingRoomAmountColor'),
        'waitingRoomPositiveFeedbackColor' => $this->getParameter('vaszev_barion.waitingRoomPositiveFeedbackColor'),
        'waitingRoomNegativeFeedbackColor' => $this->getParameter('vaszev_barion.waitingRoomNegativeFeedbackColor'),
        'waitingRoomNeturalFeedbackColor'  => $this->getParameter('vaszev_barion.waitingRoomNeturalFeedbackColor'),
        'pixelId'                          => $this->getParameter('vaszev_barion.pixelId'),
    ];
    try {
      if ($tries > self::WAITING_ROOM_TRY) {
        $tries = self::WAITING_ROOM_TRY;
      }
      if (empty($paymentId) && empty($paymentId = $request->query->get('paymentId', null))) {
        throw new \Exception('Missing paymentId');
      }
      $paymentResponseRepo = $this->getDoctrine()->getRepository(BarionPaymentResponse::class);
      $paymentResponse = $paymentResponseRepo->findOneBy(['PaymentId' => $paymentId]);
      if (empty($paymentResponse)) {
        throw new \Exception('Invalid paymentId');
      }
      $paymentStateResponse = $barion->paymentState($paymentId);
      $ret['tries'] = $tries - 1;
      $ret['paymentStateResponse'] = $paymentStateResponse;
      $ret['judgedRefresh'] = (Barion::judgeRefresh($paymentStateResponse->getStatus()) && $tries > 0);
      $ret['judgedStatus'] = Barion::judgeStatus($paymentStateResponse->getStatus());
      $ret['judgedStep'] = Barion::judgeStep($paymentStateResponse->getStatus());
    } catch (\Exception $e) {
      $ret['msg'] = $e->getMessage();
    }

    return $ret;
  }



  /**
   * Whenever a given payment's state changes, it is expected that the caller system and the Barion database are synchronized. This is accomplished by implementing the callback mechanism (referred to as "Instant Payment Notification" or "IPN" in some terminology) between the two systems.
   * @param Request $request
   * @Route("/callback", name="barion_callback")
   * @return Response
   */
  public function callback(Request $request, Barion $barion) {
    $response = new Response();
    try {
      $paymentId = $request->request->get('paymentId', null);
      if (empty($paymentId)) {
        throw new \Exception('Missing paymentId');
      }
      $responseRepo = $this->getDoctrine()->getRepository(BarionPaymentResponse::class);
      $valid = $responseRepo->findOneBy(['PaymentId' => $paymentId]);
      if (empty($valid)) {
        throw new \Exception('Invalid paymentId');
      }
      // get and store actual state of payment
      $barion->paymentState($paymentId);
      $response->setStatusCode(200);
    } catch (\Exception $e) {
      $response->setStatusCode(403)
               ->setContent($e->getMessage());
    }

    return $response;
  }



  /**
   * render this into any of your webshop pages
   * @Template("@VaszevBarion/barion/pixel.html.twig")
   */
  public function pixel() {
    $ret = ['pixelId' => $this->getParameter('vaszev_barion.pixelId'),];

    return $ret;
  }

}