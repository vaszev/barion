<?php

namespace Vaszev\BarionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Vaszev\BarionBundle\Repository\BarionPaymentStateResponseRepository")
 */
class BarionPaymentStateResponse extends Base {

  /**
   * @var string
   * @ORM\Column(name="payment_id", type="string", length=255)
   */
  private $PaymentId;

  /**
   * @ORM\ManyToOne(targetEntity="BarionPaymentRequest", inversedBy="PaymentStates", cascade={"persist"})
   */
  private $PaymentRequest;

  /**
   * @var string
   * OrderNumber: "wsm-19/5/14-3"
   * @ORM\Column(name="order_number", type="string", length=255)
   */
  private $OrderNumber;

  /**
   * @var string
   * @ORM\Column(name="posid", type="string", length=255)
   */
  private $POSId;

  /**
   * @var string
   * @ORM\Column(name="posname", type="string", length=255)
   */
  private $POSName;

  /**
   * @var string
   * @ORM\Column(name="posowner_email", type="string", length=255)
   */
  private $POSOwnerEmail;

  /**
   * @var string
   * @ORM\Column(name="status", type="string", length=255)
   */
  private $Status;

  /**
   * @var string
   * @ORM\Column(name="payment_type", type="string", length=255)
   */
  private $PaymentType;

  /**
   * @var string
   * @ORM\Column(name="funding_source", type="string", length=255, nullable=true)
   */
  private $FundingSource = null;

  /**
   * currently disabled
   */
  private $FundingInformation = null;

  /**
   * @var array
   * @ORM\Column(name="allowed_funding_sources", type="array")
   */
  private $AllowedFundingSources;

  /**
   * @var bool
   * @ORM\Column(name="guest_checkout", type="boolean")
   */
  private $GuestCheckout;

  /**
   * @var \DateTime
   * @ORM\Column(name="created_at", type="datetime")
   */
  private $CreatedAt;

  /**
   * @var \DateTime
   * @ORM\Column(name="valid_until", type="datetime")
   */
  private $ValidUntil;

  /**
   * @var \DateTime
   * @ORM\Column(name="completed_at", type="datetime")
   */
  private $CompletedAt;

  /**
   * @var \DateTime
   * @ORM\Column(name="reserved_until", type="datetime")
   */
  private $ReservedUntil;

  /**
   * @var float
   * @ORM\Column(name="total", type="decimal", precision=17, scale=2)
   */
  private $Total;

  /**
   * @var string
   * @ORM\Column(name="currency", type="string", length=255)
   */
  private $Currency;

  /**
   * currently disabled
   */
  private $Transactions;

  /**
   * @var string
   * @ORM\Column(name="recurrence_result", type="string", length=255, nullable=true)
   */
  private $RecurrenceResult = null;

  /**
   * @var string
   * @ORM\Column(name="suggested_locale", type="string", length=255)
   */
  private $SuggestedLocale;

  /**
   * @var int
   * @ORM\Column(name="fraud_risk_score", type="integer", nullable=true)
   */
  private $FraudRiskScore = null;

  /**
   * @var string
   * @ORM\Column(name="redirect_url", type="string", length=255)
   */
  private $RedirectUrl;

  /**
   * it could be null when ENV == dev (otherwise Barion keep sending emails periodically)
   * @var string
   * @ORM\Column(name="callback_url", type="string", length=255, nullable=true)
   */
  private $CallbackUrl = null;



  /**
   * @return string
   */
  public function getPaymentId(): ?string {
    return $this->PaymentId;
  }



  /**
   * @param string $PaymentId
   * @return $this
   */
  public function setPaymentId($PaymentId) {
    $this->PaymentId = $PaymentId;

    return $this;
  }



  /**
   * @return mixed
   */
  public function getPaymentRequest() {
    return $this->PaymentRequest;
  }



  /**
   * @param BarionPaymentRequest $paymentRequest
   * @return $this
   */
  public function setPaymentRequest($paymentRequest) {
    $paymentRequest->getPaymentResponse()->setStatus($this->getStatus());
    $this->PaymentRequest = $paymentRequest;

    return $this;
  }



  /**
   * @return string
   */
  public function getOrderNumber(): ?string {
    return $this->OrderNumber;
  }



  /**
   * @param string $OrderNumber
   * @return $this
   */
  public function setOrderNumber($OrderNumber) {
    $this->OrderNumber = $OrderNumber;

    return $this;
  }



  /**
   * @return string
   */
  public function getPOSId(): ?string {
    return $this->POSId;
  }



  /**
   * @param string $POSId
   * @return $this
   */
  public function setPOSId($POSId) {
    $this->POSId = $POSId;

    return $this;
  }



  /**
   * @return string
   */
  public function getPOSName(): ?string {
    return $this->POSName;
  }



  /**
   * @param string $POSName
   * @return $this
   */
  public function setPOSName($POSName) {
    $this->POSName = $POSName;

    return $this;
  }



  /**
   * @return string
   */
  public function getPOSOwnerEmail(): ?string {
    return $this->POSOwnerEmail;
  }



  /**
   * @param string $POSOwnerEmail
   * @return $this
   */
  public function setPOSOwnerEmail($POSOwnerEmail) {
    $this->POSOwnerEmail = $POSOwnerEmail;

    return $this;
  }



  /**
   * @return string
   */
  public function getStatus(): ?string {
    return $this->Status;
  }



  /**
   * @param string $Status
   * @return $this
   */
  public function setStatus($Status) {
    $this->Status = $Status;

    return $this;
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
   * @return string
   */
  public function getFundingSource(): ?string {
    return $this->FundingSource;
  }



  /**
   * @param string $FundingSource
   * @return $this
   */
  public function setFundingSource($FundingSource) {
    $this->FundingSource = $FundingSource;

    return $this;
  }



  /**
   * @return array
   */
  public function getAllowedFundingSources(): ?array {
    return $this->AllowedFundingSources;
  }



  /**
   * @param array $AllowedFundingSources
   * @return $this
   */
  public function setAllowedFundingSources($AllowedFundingSources) {
    $this->AllowedFundingSources = $AllowedFundingSources;

    return $this;
  }



  /**
   * @return bool
   */
  public function isGuestCheckout(): ?bool {
    return $this->GuestCheckout;
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
   * @return \DateTime
   */
  public function getCreatedAt(): ?\DateTime {
    return $this->CreatedAt;
  }



  /**
   * @param $CreatedAt
   * @return $this
   * @throws \Exception
   */
  public function setCreatedAt($CreatedAt) {
    $this->CreatedAt = new \DateTime($CreatedAt);

    return $this;
  }



  /**
   * @return \DateTime
   */
  public function getValidUntil(): ?\DateTime {
    return $this->ValidUntil;
  }



  /**
   * @param $ValidUntil
   * @return $this
   * @throws \Exception
   */
  public function setValidUntil($ValidUntil) {
    $this->ValidUntil = new \DateTime($ValidUntil);

    return $this;
  }



  /**
   * @return \DateTime
   */
  public function getCompletedAt(): ?\DateTime {
    return $this->CompletedAt;
  }



  /**
   * @param $CompletedAt
   * @return $this
   * @throws \Exception
   */
  public function setCompletedAt($CompletedAt) {
    $this->CompletedAt = new \DateTime($CompletedAt);

    return $this;
  }



  /**
   * @return \DateTime
   */
  public function getReservedUntil(): ?\DateTime {
    return $this->ReservedUntil;
  }



  /**
   * @param $ReservedUntil
   * @return $this
   * @throws \Exception
   */
  public function setReservedUntil($ReservedUntil) {
    $this->ReservedUntil = new \DateTime($ReservedUntil);

    return $this;
  }



  /**
   * @return float
   */
  public function getTotal(): ?float {
    return $this->Total;
  }



  /**
   * @param float $Total
   * @return $this
   */
  public function setTotal($Total) {
    $this->Total = $Total;

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



  /**
   * @return string
   */
  public function getRecurrenceResult(): ?string {
    return $this->RecurrenceResult;
  }



  /**
   * @param string $RecurrenceResult
   * @return $this
   */
  public function setRecurrenceResult($RecurrenceResult) {
    $this->RecurrenceResult = $RecurrenceResult;

    return $this;
  }



  /**
   * @return string
   */
  public function getSuggestedLocale(): ?string {
    return $this->SuggestedLocale;
  }



  /**
   * @param string $SuggestedLocale
   * @return $this
   */
  public function setSuggestedLocale($SuggestedLocale) {
    $this->SuggestedLocale = $SuggestedLocale;

    return $this;
  }



  /**
   * @return int
   */
  public function getFraudRiskScore(): ?int {
    return $this->FraudRiskScore;
  }



  /**
   * @param int $FraudRiskScore
   * @return $this
   */
  public function setFraudRiskScore($FraudRiskScore) {
    $this->FraudRiskScore = $FraudRiskScore;

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


}
