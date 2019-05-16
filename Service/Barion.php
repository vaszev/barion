<?php

namespace Vaszev\BarionBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vaszev\BarionBundle\BarionLibrary\ApiErrorModel;
use Vaszev\BarionBundle\BarionLibrary\BarionClient;
use Vaszev\BarionBundle\BarionLibrary\ItemModel;
use Vaszev\BarionBundle\BarionLibrary\PaymentTransactionModel;
use Vaszev\BarionBundle\BarionLibrary\PreparePaymentRequestModel;
use Vaszev\BarionBundle\Entity\BarionItem;
use Vaszev\BarionBundle\Entity\BarionPaymentRequest;
use Vaszev\BarionBundle\Entity\BarionPaymentResponse;
use Vaszev\BarionBundle\Entity\BarionPaymentStateResponse;
use Vaszev\BarionBundle\Entity\BarionPaymentTransaction;

// region +CONSTANTS

abstract class BarionEnvironment {
  const Test = "test";
  const Prod = "prod";
}

abstract class PaymentType {
  const Immediate = "Immediate";
  const Reservation = "Reservation";
}

abstract class FundingSourceType {
  const All = "All";
  const Balance = "Balance";
  const Bankcard = "Bankcard";
}

abstract class PaymentStatus {
  // 10
  const Prepared = "Prepared";
  // 20
  const Started = "Started";
  // 21
  const InProgress = "InProgress";
  // 25
  const Reserved = "Reserved";
  // 30
  const Canceled = "Canceled";
  // 40
  const Succeeded = "Succeeded";
  // 50
  const Failed = "Failed";
  // 60
  const PartiallySucceeded = "PartiallySucceeded";
  // 70
  const Expired = "Expired";
}

abstract class QRCodeSize {
  const Small = "Small";
  const Normal = "Normal";
  const Large = "Large";
}

abstract class RecurrenceResult {
  const None = "None";
  const Successful = "Successful";
  const Failed = "Failed";
  const NotFound = "NotFound";
}

abstract class UILocale {
  const HU = "hu-HU";
  const EN = "en-US";
  const DE = "de-DE";
  const SL = "sl-SI";
  const SK = "sk-SK";
  const FR = "fr-FR";
  const CZ = "cs-CZ";
  const GR = "el-GR";
}

abstract class Currency {
  const HUF = "HUF";
  const EUR = "EUR";
  const USD = "USD";
  const CZK = "CZK";



  public static function isValid($name) {
    $class = new ReflectionClass(__CLASS__);
    $constants = $class->getConstants();

    return array_key_exists($name, $constants);
  }
}

abstract class CardType {
  const Unknown = "Unknown";
  const Mastercard = "Mastercard";
  const Maestro = "Maestro";
  const Visa = "Visa";
  const Electron = "Electron";
  const AmericanExpress = "AmericanExpress";
}

// endregion

class Barion {

  private $translator;
  private $container;
  private $em;
  private $validator;
  private $redirectURL;
  private $webshopName;
  /** @var string */
  private $currency;
  /** @var BarionPaymentTransaction */
  private $transaction;
  /** @var BarionItem[] */
  private $items = [];
  /** @var BarionPaymentRequest */
  private $request;
  /** @var BarionClient */
  private $client;

  // region +JUDGES



  /**
   * return as 1: positive, 0: netural, -1: negative
   * @param $status
   * @return int|null
   */
  static function judgeStatus($status): int {
    switch ($status) {
      case PaymentStatus::Canceled:
      case PaymentStatus::Expired:
      case PaymentStatus::Failed:
        return -1;
        break;
      case PaymentStatus::Succeeded:
        return 1;
        break;
      case PaymentStatus::InProgress:
      case PaymentStatus::PartiallySucceeded:
      case PaymentStatus::Prepared:
      case PaymentStatus::Reserved:
      case PaymentStatus::Started:
        return 0;
        break;
    }

    return 0;
  }



  /**
   * @param $status
   * @return int
   */
  static function judgeStep($status): int {
    switch ($status) {
      case PaymentStatus::InProgress:
      case PaymentStatus::PartiallySucceeded:
      case PaymentStatus::Reserved:
      case PaymentStatus::Canceled:
      case PaymentStatus::Expired:
      case PaymentStatus::Failed:
        return 2;
        break;
      case PaymentStatus::Succeeded:
        return 3;
        break;
      case PaymentStatus::Prepared:
      case PaymentStatus::Started:
        return 1;
        break;
    }

    return 1;
  }



  /**
   * @param $status
   * @return bool
   */
  static function judgeRefresh($status): bool {
    switch ($status) {
      case PaymentStatus::InProgress:
      case PaymentStatus::PartiallySucceeded:
      case PaymentStatus::Reserved:
      case PaymentStatus::Prepared:
      case PaymentStatus::Started:
        return true;
        break;
      case PaymentStatus::Failed:
      case PaymentStatus::Expired:
      case PaymentStatus::Succeeded:
      case PaymentStatus::Canceled:
        return false;
        break;
    }

    return false;
  }



  // endregion

  public function __construct(TranslatorInterface $translator, ContainerInterface $container, EntityManagerInterface $em, ValidatorInterface $validator) {
    $this->translator = $translator;
    $this->container = $container;
    $this->em = $em;
    $this->validator = $validator;
  }



  public function reset() {
    $this->redirectURL = null;
    $this->webshopName = null;
    $this->currency = null;
    $this->transaction = null;
    $this->items = [];
    $this->request = null;
    $this->client = null;
  }



  /**
   * @param $redirectURL
   * @param string $currency
   * @return $this
   * @throws \Exception
   */
  public function initShopping($redirectURL, $currency = Currency::HUF) {
    $posKey = $this->container->getParameter('vaszev_barion.posKey');
    $apiVersion = $this->container->getParameter('vaszev_barion.apiVersion');
    $sandbox = $this->container->getParameter('vaszev_barion.sandbox');
    $webshopName = $this->container->getParameter('vaszev_barion.webshopName');
    $this->reset();
    $this->webshopName = $webshopName;
    $this->redirectURL = $redirectURL;
    $this->currency = $currency;
    $this->client = new BarionClient($posKey, $apiVersion, ($sandbox ? BarionEnvironment::Test : BarionEnvironment::Prod));

    return $this;
  }



  /**
   * @param $paymentId
   * @return BarionPaymentStateResponse
   * @throws \Exception
   */
  public function paymentState($paymentId) {
    $posKey = $this->container->getParameter('vaszev_barion.posKey');
    $apiVersion = $this->container->getParameter('vaszev_barion.apiVersion');
    $sandbox = $this->container->getParameter('vaszev_barion.sandbox');
    $client = new BarionClient($posKey, $apiVersion, ($sandbox ? BarionEnvironment::Test : BarionEnvironment::Prod));
    $paymentDetails = $client->GetPaymentState($paymentId);
    if (empty($paymentDetails) || $paymentDetails->Errors) {
      throw new \Exception("Unable to get payment state details.");
    }
    // save if needed
    $paymentRequestRepo = $this->em->getRepository(BarionPaymentRequest::class);
    $paymentStateResponseRepo = $this->em->getRepository(BarionPaymentStateResponse::class);
    $paymentStateResponse = $paymentStateResponseRepo->findOneBy(['PaymentId' => $paymentId, 'Status' => $paymentDetails->Status]);
    if (empty($paymentStateResponse)) {
      $paymentStateResponse = new BarionPaymentStateResponse();
    }
    $paymentStateResponse->setPaymentId($paymentDetails->PaymentId)
                         ->setStatus($paymentDetails->Status)
                         ->setPaymentRequest($paymentRequestRepo->find((int)$paymentDetails->PaymentRequestId))
                         ->setOrderNumber($paymentDetails->OrderNumber)
                         ->setPOSId($paymentDetails->POSId)
                         ->setPOSName($paymentDetails->POSName)
                         ->setPOSOwnerEmail($paymentDetails->POSOwnerEmail)
                         ->setPaymentType($paymentDetails->PaymentType)
                         ->setFundingSource($paymentDetails->FundingSource)
                         ->setAllowedFundingSources($paymentDetails->AllowedFundingSources)
                         ->setGuestCheckout($paymentDetails->GuestCheckout)
                         ->setCreatedAt($paymentDetails->CreatedAt)
                         ->setValidUntil($paymentDetails->ValidUntil)
                         ->setCompletedAt($paymentDetails->CompletedAt)
                         ->setReservedUntil($paymentDetails->ReservedUntil)
                         ->setTotal($paymentDetails->Total)
                         ->setCurrency($paymentDetails->Currency)
                         ->setRecurrenceResult($paymentDetails->RecurrenceResult)
                         ->setSuggestedLocale($paymentDetails->SuggestedLocale)
                         ->setFraudRiskScore($paymentDetails->FraudRiskScore)
                         ->setRedirectUrl($paymentDetails->RedirectUrl)
                         ->setCallbackUrl($paymentDetails->CallbackUrl);
    try {
      $this->em->persist($paymentStateResponse);
      $this->em->flush();
    } catch (\Exception $e) {
      throw new \Exception('Payment state cannot be flushed.');
    }

    return $paymentStateResponse;
  }



  /**
   * @param $name
   * @param int $quantity
   * @param float $unitPrice
   * @param $sku
   * @param string $unit
   * @param null $description
   * @return $this
   * @throws \Exception
   */
  public function addItem($name, $description, int $quantity, float $unitPrice, $sku, $unit = 'piece') {
    if (empty($this->transaction)) {
      throw new \Exception('Transaction not found. Call "createTransaction" first');
    }
    foreach ($this->items as $item) {
      if ($item->getSKU() == $sku) {
        throw new \Exception('SKU (' . $sku . ') already exists at item (' . $name . ')');
      }
    }
    $item = new BarionItem();
    $item->setName($name)
         ->setDescription($description)
         ->setQuantity($quantity)
         ->setUnit($unit)
         ->setUnitPrice($unitPrice)
         ->setItemTotal($quantity * $unitPrice)
         ->setSKU($sku);
    $this->items[] = $item;

    return $this;
  }



  /**
   * @param int $connetedOrderId
   * @param null $comment
   * @return $this
   * @throws \Exception
   */
  public function createTransaction(int $connetedOrderId, $comment = null) {
    if (empty($this->client)) {
      throw new \Exception('Wrapper not initialized. Call "init" first');
    }
    $transaction = new BarionPaymentTransaction();
    $transaction->setPayee($this->container->getParameter('vaszev_barion.payee'))
                ->setCurrency($this->currency)
                ->setConnectedOrderId($connetedOrderId)
                ->setComment($comment);
    $this->transaction = $transaction;

    return $this;
  }



  /**
   * @param string $payerHint
   * @param string $shippingAddress
   * @param string $locale
   * @return $this
   * @throws \Exception
   */
  public function preparePaymentRequest($payerHint = 'user@example.com', $shippingAddress = '12345 NJ, Example ave. 6.', $locale = UILocale::HU) {
    if (empty($this->client)) {
      throw new \Exception('Wrapper not initialized. Call "init" first');
    }
    if (empty($this->transaction)) {
      throw new \Exception('Empty transaction. Call "createTransaction" first');
    }
    $request = new BarionPaymentRequest();
    $request->setGuestCheckout(true)
            ->setPaymentType(PaymentType::Immediate)
            ->setFundingSources([FundingSourceType::All])
            ->setPayerHint($payerHint)
            ->setLocale($locale)
            ->setCurrency($this->currency)
            ->setShippingAddress($shippingAddress)
            ->setSavedRedirectUrl($this->redirectURL)
            ->setRedirectUrl($this->container->get('router')->generate('bairon_waiting_room', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCallbackUrl($this->container->get('router')->generate('barion_callback', [], UrlGeneratorInterface::ABSOLUTE_URL));
    $this->request = $request;

    return $this;
  }



  /**
   * @return BarionPaymentTransaction
   * @throws \Exception
   */
  private function prepareVerifiedTransaction() {
    if (empty($this->client)) {
      throw new \Exception('Wrapper not initialized. Call "init" first');
    }
    if (empty($this->request)) {
      throw new \Exception('Empty request. Call "preparePaymentRequest" first');
    }
    if (empty($this->transaction)) {
      throw new \Exception('Transaction not found. Call "createTransaction" first');
    }
    if (empty($this->items)) {
      throw new \Exception('Item not found. Call "addItem" first');
    }
    // save request and transaction
    $this->transaction->setRequest($this->request)
                      ->setItems($this->items);
    $errors = $this->validator->validate($this->transaction);
    if (count($errors) > 0) {
      $errorsString = (string)$errors;
      throw new \Exception($errorsString);
    }
    try {
      $this->em->persist($this->transaction);
      $this->em->flush();
    } catch (\Exception $e) {
      throw new \Exception('Transaction / Request / Item cannot be flushed. Check mandatory fields');
    }

    return $this->transaction;
  }



  /**
   * @return string
   * @throws \Exception
   */
  public function closeAndGetPaymentURL() {
    $env = $_SERVER['APP_ENV'] ?? 'dev';
    $paymentTransaction = $this->prepareVerifiedTransaction();
    $trans = new PaymentTransactionModel();
    $trans->POSTransactionId = $paymentTransaction->getId();
    $trans->Payee = $paymentTransaction->getPayee();
    $trans->Total = $paymentTransaction->getTotal();
    $trans->Currency = $paymentTransaction->getCurrency();
    $trans->Comment = $paymentTransaction->getComment();
    /** @var BarionItem $item */
    foreach ($paymentTransaction->getItems() as $item) {
      $itemModel = new ItemModel();
      $itemModel->Name = $item->getName();
      $itemModel->Description = $item->getDescription();
      $itemModel->Quantity = $item->getQuantity();
      $itemModel->Unit = $item->getUnit();
      $itemModel->UnitPrice = $item->getUnitPrice();
      $itemModel->ItemTotal = $item->getItemTotal();
      $itemModel->SKU = $item->getSKU();
      $trans->AddItem($itemModel);
    }
    /** @var BarionPaymentRequest $paymentRequest */
    $paymentRequest = $paymentTransaction->getRequest();
    $ppr = new PreparePaymentRequestModel();
    $ppr->GuestCheckout = $paymentRequest->isGuestCheckout();
    $ppr->PaymentType = $paymentRequest->getPaymentType();
    $ppr->FundingSources = $paymentRequest->getFundingSources();
    $ppr->PaymentRequestId = $paymentRequest->getId();
    $ppr->PayerHint = $paymentRequest->getPayerHint();
    $ppr->Locale = $paymentRequest->getLocale();
    $ppr->OrderNumber = implode("-", [$this->webshopName, date('y/n/j'), $paymentRequest->getId()]);
    $ppr->Currency = $paymentRequest->getCurrency();
    $ppr->ShippingAddress = $paymentRequest->getShippingAddress();
    $ppr->RedirectUrl = $paymentRequest->getRedirectUrl();
    if ($env == 'dev') {
      $ppr->CallbackUrl = null;
    } else {
      $ppr->CallbackUrl = $paymentRequest->getCallbackUrl();
    }
    $ppr->AddTransaction($trans);
    // prepare for sending
    $myPayment = $this->client->PreparePayment($ppr);
    if (empty($myPayment)) {
      throw new \Exception('Empty response from Barion client');
    }
    if (!empty($myPayment->Errors)) {
      /** @var ApiErrorModel $firstError */
      $firstError = current($myPayment->Errors);
      throw new \Exception($firstError->Title . ' / ' . $firstError->Description);
    }
    // store response
    $paymentRequestRepo = $this->em->getRepository(BarionPaymentRequest::class);
    $response = new BarionPaymentResponse();
    $response->setPaymentId($myPayment->PaymentId)
             ->setPaymentRequest($paymentRequestRepo->find((int)$myPayment->PaymentRequestId))
             ->setStatus($myPayment->Status)
             ->setQRUrl($myPayment->QRUrl)
             ->setRecurrenceResult($myPayment->RecurrenceResult)
             ->setPaymentRedirectUrl($myPayment->PaymentRedirectUrl);
    try {
      $this->em->persist($response);
      $this->em->flush();
    } catch (\Exception $e) {
      throw new \Exception('Response cannot be flushed.');
    }
    $this->reset();

    return $myPayment->PaymentRedirectUrl;
  }



  /**
   * @param int $myOrderId
   * @return bool
   */
  public function checkMyOrderBeingPaid(int $myOrderId): bool {
    try {
      $paymentTransactionRepo = $this->em->getRepository(BarionPaymentTransaction::class);
      $transaction = $paymentTransactionRepo->findOneBy(['ConnectedOrderId' => $myOrderId]);
      if (empty($transaction)) {
        throw new \Exception('transaction not found');
      }
      $request = $transaction->getRequest();
      $response = $request->getPaymentResponse();
      $status = $response->getStatus();
      if ($status != PaymentStatus::Succeeded) {
        throw new \Exception('not paid (yet)');
      }

      return true;
    } catch (\Exception $e) {
      return false;
    }
  }

}