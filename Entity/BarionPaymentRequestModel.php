<?php

namespace Vaszev\BarionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vaszev\BarionBundle\Service\Currency;
use Vaszev\BarionBundle\Service\FundingSourceType;
use Vaszev\BarionBundle\Service\PaymentType;
use Symfony\Component\Validator\Constraints as Assert;
use Vaszev\BarionBundle\Service\UILocale;

/**
 * @ORM\Entity(repositoryClass="Vaszev\BarionBundle\Repository\BarionPaymentRequestModelRepository")
 */
class BarionPaymentRequestModel extends Base {

  /**
   * @var string
   * @ORM\Column(name="payment_type", type="string", length=255)
   */
  private $PaymentType = PaymentType::Immediate;

  /**
   * @var string
   * TimeSpan (d:hh:mm:ss)
   * Required only if PaymentType is "Reservation"
   * Minimum value: one minute
   * Maximum value: one year
   * Default value: 30 minutes
   */
  private $ReservationPeriod;

  /**
   * @var string
   * TimeSpan (d:hh:mm:ss)
   * Time window for the payment to be completed. The payer must execute the payment before this elapses, or else the payment will expire and can no longer be completed.
   * Optional
   * Minimum value: one minute
   * Maximum value: one week
   * Default value: 30 minutes
   */
  private $PaymentWindow;

  /**
   * @var bool
   * Flag indicating wether the payment can be completed without a registered Barion wallet. Guest checkout can only be done with bank cards, and the payer must supply a valid e-mail address - this is necessary for fraud control.
   * @ORM\Column(name="guest_checkout", type="boolean")
   */
  private $GuestCheckout = true;

  /**
   * @var array
   * An array of strings containing the allowed funding sources that can be used to complete the payment. "Balance" means that the payer can only use their Barion wallet balance, while "All" means the payment can be completed with either a Barion wallet or a bank card.
   * @ORM\Column(name="funding_sources", type="array")
   */
  private $FundingSources = [FundingSourceType::All];

  /**
   * @var string
   * The shop can optionally supply an e-mail address as a hint on who should complete the payment. This can be used if the shop is certain about that the payer has an active Barion wallet or the shop would like to help the guest payer with filling in the email field for her/him. If provided, the Barion Smart Gateway automatically fills out the e-mail address field in the Barion wallet login form and the guest payer form, speeding up the payment process.
   * @ORM\Column(name="payer_hint", type="string", length=255)
   * @Assert\Email()
   */
  private $PayerHint;

  /**
   * @ORM\OneToMany(targetEntity="Vaszev\BarionBundle\Entity\BarionPaymentTransactionModel", mappedBy="RequestModel")
   * @Assert\Count(min=1)
   */
  private $Transactions;

  /**
   * @var string
   * @ORM\Column(name="locale", type="string", length=255)
   */
  private $Locale = UILocale::HU;

  /**
   * @var string
   * @ORM\Column(name="shipping_address", type="string", length=255)
   */
  private $ShippingAddress;

  /**
   * @var bool
   * This flag indicates that the shop would like to initialize a token payment. This means that the shop is authorized to charge the funding source of the payer in the future without redirecting her/him to the Barion Smart Gateway. It can be used for one-click and susbscription payment scenarios.
   */
  private $InitiateRecurrence;

  /**
   * @var string
   * A string used to identify a given authorized payment. Its purpose is determined by the value of the InitiateRecurrence property.
   * If InitiateRecurrence is true, this property must contain a new desired identifier for a new authorized payment. This should be generated and stored by the shop before calling the  API. Also the shop must ensure that this is unique per user in its own system.
   * If InitiateRecurrence is false, this property must contain an existing identifier for an authorized payment. This should be used to charge a payer's funding source (either bank card or Barion wallet) that was already used successfully for a payment in the shop.
   */
  private $RecurrenceId;

  /**
   * @var string
   * @ORM\Column(name="redirect_url", type="string", length=255)
   */
  private $RedirectUrl;

  /**
   * @var string
   * @ORM\Column(name="callback_url", type="string", length=255)
   */
  private $CallbackUrl;

  /**
   * @var string
   * @ORM\Column(name="currency", type="string", length=255)
   */
  private $Currency = Currency::HUF;

  /**
   * @ORM\OneToOne(targetEntity="Vaszev\BarionBundle\Entity\BarionPaymentResponseModel", mappedBy="PaymentRequestId")
   */
  private $PaymentResponseId;



  /**
   * BarionPaymentRequestModel constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->Transactions = new ArrayCollection();
  }



  /**
   * @return mixed
   */
  public function getPaymentResponseId() {
    return $this->PaymentResponseId;
  }



  /**
   * @return mixed
   */
  public function getTransactions() {
    return $this->Transactions;
  }



  /**
   * @return string
   */
  public function getPaymentType(): ?string {
    return $this->PaymentType;
  }



  /**
   * @param string $PaymentType
   * @return $this
   */
  public function setPaymentType($PaymentType) {
    $this->PaymentType = $PaymentType;

    return $this;
  }



  /**
   * @return mixed
   */
  public function getReservationPeriod() {
    return $this->ReservationPeriod;
  }



  /**
   * @param mixed $ReservationPeriod
   * @return $this
   */
  public function setReservationPeriod($ReservationPeriod) {
    $this->ReservationPeriod = $ReservationPeriod;

    return $this;
  }



  /**
   * @return mixed
   */
  public function getPaymentWindow() {
    return $this->PaymentWindow;
  }



  /**
   * @param mixed $PaymentWindow
   * @return $this
   */
  public function setPaymentWindow($PaymentWindow) {
    $this->PaymentWindow = $PaymentWindow;

    return $this;
  }



  /**
   * @return bool
   */
  public function isGuestCheckout(): bool {
    return (bool)$this->GuestCheckout;
  }



  /**
   * @param bool $GuestCheckout
   * @return $this
   */
  public function setGuestCheckout($GuestCheckout) {
    $this->GuestCheckout = $GuestCheckout;

    return $this;
  }



  /**
   * @return array
   */
  public function getFundingSources(): ?array {
    return $this->FundingSources;
  }



  /**
   * @param array $FundingSources
   * @return $this
   */
  public function setFundingSources($FundingSources) {
    $this->FundingSources = $FundingSources;

    return $this;
  }



  /**
   * @return string
   */
  public function getPayerHint(): ?string {
    return $this->PayerHint;
  }



  /**
   * @param string $PayerHint
   * @return $this
   */
  public function setPayerHint($PayerHint) {
    $this->PayerHint = $PayerHint;

    return $this;
  }



  /**
   * @return string
   */
  public function getLocale(): ?string {
    return $this->Locale;
  }



  /**
   * @param string $Locale
   * @return $this
   */
  public function setLocale($Locale) {
    $this->Locale = $Locale;

    return $this;
  }



  /**
   * @return string
   */
  public function getShippingAddress(): ?string {
    return $this->ShippingAddress;
  }



  /**
   * @param string $ShippingAddress
   * @return $this
   */
  public function setShippingAddress($ShippingAddress) {
    $this->ShippingAddress = $ShippingAddress;

    return $this;
  }



  /**
   * @return mixed
   */
  public function getInitiateRecurrence() {
    return $this->InitiateRecurrence;
  }



  /**
   * @param mixed $InitiateRecurrence
   * @return $this
   */
  public function setInitiateRecurrence($InitiateRecurrence) {
    $this->InitiateRecurrence = $InitiateRecurrence;

    return $this;
  }



  /**
   * @return mixed
   */
  public function getRecurrenceId() {
    return $this->RecurrenceId;
  }



  /**
   * @param mixed $RecurrenceId
   * @return $this
   */
  public function setRecurrenceId($RecurrenceId) {
    $this->RecurrenceId = $RecurrenceId;

    return $this;
  }



  /**
   * @return string
   */
  public function getRedirectUrl(): ?string {
    return $this->RedirectUrl;
  }



  /**
   * @param string $RedirectUrl
   * @return $this
   */
  public function setRedirectUrl($RedirectUrl) {
    $this->RedirectUrl = $RedirectUrl;

    return $this;
  }



  /**
   * @return string
   */
  public function getCallbackUrl(): ?string {
    return $this->CallbackUrl;
  }



  /**
   * @param string $CallbackUrl
   * @return $this
   */
  public function setCallbackUrl($CallbackUrl) {
    $this->CallbackUrl = $CallbackUrl;

    return $this;
  }



  /**
   * @return string
   */
  public function getCurrency(): ?string {
    return $this->Currency;
  }



  /**
   * @param string $Currency
   * @return $this
   */
  public function setCurrency($Currency) {
    $this->Currency = $Currency;

    return $this;
  }


}
