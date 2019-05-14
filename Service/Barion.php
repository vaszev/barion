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
use Vaszev\BarionBundle\Entity\BarionItemModel;
use Vaszev\BarionBundle\Entity\BarionPaymentRequestModel;
use Vaszev\BarionBundle\Entity\BarionPaymentResponseModel;
use Vaszev\BarionBundle\Entity\BarionPaymentTransactionModel;

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
  /** @var BarionPaymentTransactionModel */
  private $transactionModel;
  /** @var BarionItemModel[] */
  private $itemModels = [];
  /** @var BarionPaymentRequestModel */
  private $requestModel;
  /** @var BarionClient */
  private $client;



  public function __construct(TranslatorInterface $translator, ContainerInterface $container, EntityManagerInterface $em, ValidatorInterface $validator) {
    $this->translator = $translator;
    $this->container = $container;
    $this->em = $em;
    $this->validator = $validator;
  }



  /**
   * @param $redirectURL
   * @param string $currency
   * @return $this
   * @throws \Exception
   */
  public function init($redirectURL, $currency = Currency::HUF) {
    $posKey = $this->container->getParameter('vaszev_barion.posKey');
    $apiVersion = $this->container->getParameter('vaszev_barion.apiVersion');
    $sandbox = $this->container->getParameter('vaszev_barion.sandbox');
    $webshopName = $this->container->getParameter('vaszev_barion.webshopName');
    $this->webshopName = $webshopName;
    $this->redirectURL = $redirectURL;
    $this->currency = $currency;
    $this->client = new BarionClient($posKey, $apiVersion, ($sandbox ? BarionEnvironment::Test : BarionEnvironment::Prod));

    return $this;
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
    if (empty($this->transactionModel)) {
      throw new \Exception('Transaction model not found. Call "createTransactionModel" first');
    }
    foreach ($this->itemModels as $itemModel) {
      if ($itemModel->getSKU() == $sku) {
        throw new \Exception('SKU (' . $sku . ') already exists at item (' . $name . ')');
      }
    }
    $itemModel = new BarionItemModel();
    $itemModel->setName($name)
              ->setDescription($description)
              ->setQuantity($quantity)
              ->setUnit($unit)
              ->setUnitPrice($unitPrice)
              ->setItemTotal($quantity * $unitPrice)
              ->setSKU($sku);
    $this->itemModels[] = $itemModel;

    return $this;
  }



  /**
   * @param null $comment
   * @return $this
   * @throws \Exception
   */
  public function createTransactionModel($comment = null) {
    if (empty($this->client)) {
      throw new \Exception('Wrapper not initialized. Call "init" first');
    }
    $transactionModel = new BarionPaymentTransactionModel();
    $transactionModel->setPayee($this->container->getParameter('vaszev_barion.payee'))
                     ->setCurrency($this->currency)
                     ->setComment($comment);
    $this->transactionModel = $transactionModel;

    return $this;
  }



  /**
   * @param string $payerHint
   * @param string $shippingAddress
   * @param string $locale
   * @return $this
   * @throws \Exception
   */
  public function preparePaymentRequestModel($payerHint = 'user@example.com', $shippingAddress = '12345 NJ, Example ave. 6.', $locale = UILocale::HU) {
    if (empty($this->client)) {
      throw new \Exception('Wrapper not initialized. Call "init" first');
    }
    if (empty($this->transactionModel)) {
      throw new \Exception('Empty transaction model. Call "createTransactionModel" first');
    }
    $requestModel = new BarionPaymentRequestModel();
    $requestModel->setGuestCheckout(true)
                 ->setPaymentType(PaymentType::Immediate)
                 ->setFundingSources([FundingSourceType::All])
                 ->setPayerHint($payerHint)
                 ->setLocale($locale)
                 ->setCurrency($this->currency)
                 ->setShippingAddress($shippingAddress)
                 ->setRedirectUrl($this->redirectURL)
                 ->setCallbackUrl($this->container->get('router')->generate('barion_callback', [], UrlGeneratorInterface::ABSOLUTE_URL));
    $this->requestModel = $requestModel;

    return $this;
  }



  /**
   * @return BarionPaymentTransactionModel
   * @throws \Exception
   */
  private function prepareVerifiedTransaction() {
    if (empty($this->client)) {
      throw new \Exception('Wrapper not initialized. Call "init" first');
    }
    if (empty($this->requestModel)) {
      throw new \Exception('Empty request model. Call "preparePaymentRequestModel" first');
    }
    if (empty($this->transactionModel)) {
      throw new \Exception('Transaction model not found. Call "createTransactionModel" first');
    }
    if (empty($this->itemModels)) {
      throw new \Exception('Item model not found. Call "addItem" first');
    }
    // save requestmodel and transaction
    $this->transactionModel->setRequestModel($this->requestModel)
                           ->setItems($this->itemModels);
    $errors = $this->validator->validate($this->transactionModel);
    if (count($errors) > 0) {
      $errorsString = (string)$errors;
      throw new \Exception($errorsString);
    }
    try {
      $this->em->persist($this->transactionModel);
      $this->em->flush();
    } catch (\Exception $e) {
      throw new \Exception('Transaction model / Request model / Item model cannot be flushed. Check mandatory fields');
    }

    return $this->transactionModel;
  }



  /**
   * @throws \Exception
   */
  public function send() {
    $paymentTrModel = $this->prepareVerifiedTransaction();
    $trans = new PaymentTransactionModel();
    $trans->POSTransactionId = $paymentTrModel->getId();
    $trans->Payee = $paymentTrModel->getPayee();
    $trans->Total = $paymentTrModel->getTotal();
    $trans->Currency = $paymentTrModel->getCurrency();
    $trans->Comment = $paymentTrModel->getComment();
    /** @var BarionItemModel $itemModel */
    foreach ($paymentTrModel->getItems() as $itemModel) {
      $item = new ItemModel();
      $item->Name = $itemModel->getName();
      $item->Description = $itemModel->getDescription();
      $item->Quantity = $itemModel->getQuantity();
      $item->Unit = $itemModel->getUnit();
      $item->UnitPrice = $itemModel->getUnitPrice();
      $item->ItemTotal = $itemModel->getItemTotal();
      $item->SKU = $itemModel->getSKU();
      $trans->AddItem($item);
    }
    /** @var BarionPaymentRequestModel $paymentRModel */
    $paymentRModel = $paymentTrModel->getRequestModel();
    $ppr = new PreparePaymentRequestModel();
    $ppr->GuestCheckout = $paymentRModel->isGuestCheckout();
    $ppr->PaymentType = $paymentRModel->getPaymentType();
    $ppr->FundingSources = $paymentRModel->getFundingSources();
    $ppr->PaymentRequestId = $paymentRModel->getId();
    $ppr->PayerHint = $paymentRModel->getPayerHint();
    $ppr->Locale = $paymentRModel->getLocale();
    $ppr->OrderNumber = implode("-", [$this->webshopName, date('y/n/j'), $paymentRModel->getId()]);
    $ppr->Currency = $paymentRModel->getCurrency();
    $ppr->ShippingAddress = $paymentRModel->getShippingAddress();
    $ppr->RedirectUrl = $paymentRModel->getRedirectUrl();
    $ppr->CallbackUrl = $paymentRModel->getCallbackUrl();
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
    $paymentRequestRepo = $this->em->getRepository(BarionPaymentRequestModel::class);
    $responseModel = new BarionPaymentResponseModel();
    $responseModel->setPaymentId($myPayment->PaymentId)
                  ->setPaymentRequestId($paymentRequestRepo->find((int)$myPayment->PaymentRequestId))
                  ->setStatus($myPayment->Status)
                  ->setQRUrl($myPayment->QRUrl)
                  ->setRecurrenceResult($myPayment->RecurrenceResult)
                  ->setPaymentRedirectUrl($myPayment->PaymentRedirectUrl);
    try {
      $this->em->persist($responseModel);
      $this->em->flush();
    } catch (\Exception $e) {
      throw new \Exception('Response model cannot be flushed.');
    }
  }
}