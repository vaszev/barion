<?php

namespace Vaszev\BarionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Vaszev\BarionBundle\Repository\BarionPaymentResponseModelRepository")
 */
class BarionPaymentResponseModel extends Base {

  /**
   * @var string
   * @ORM\Column(name="payment_id", type="string", length=255)
   */
  private $PaymentId;

  /**
   * @ORM\OneToOne(targetEntity="Vaszev\BarionBundle\Entity\BarionPaymentRequestModel", inversedBy="PaymentResponseId")
   * @ORM\JoinColumn(name="payment_response_id", referencedColumnName="id")
   */
  private $PaymentRequestId;

  /**
   * @var string
   * @ORM\Column(name="status", type="string", length=255)
   */
  private $Status;

  /**
   * @var array
   * An array containing all transactions associated with the payment. If the Barion system deducts fees from the shop after payments, this also contains these additional fee transactions beside the payment transactions that were sent in the request.
   * currently disabled
   */
  private $Transactions;

  /**
   * @var string
   * @ORM\Column(name="qrurl", type="string", length=255)
   */
  private $QRUrl;

  /**
   * @var string
   * @ORM\Column(name="recurrence_result", type="string", length=255)
   */
  private $RecurrenceResult;

  /**
   * @var string
   * @ORM\Column(name="payment_redirect_url", type="string", length=255)
   */
  private $PaymentRedirectUrl;



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
  public function getPaymentRequestId() {
    return $this->PaymentRequestId;
  }



  /**
   * @param mixed $PaymentRequestId
   * @return $this
   */
  public function setPaymentRequestId($PaymentRequestId) {
    $this->PaymentRequestId = $PaymentRequestId;

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
  public function getQRUrl(): ?string {
    return $this->QRUrl;
  }



  /**
   * @param string $QRUrl
   * @return $this
   */
  public function setQRUrl($QRUrl) {
    $this->QRUrl = $QRUrl;

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
  public function getPaymentRedirectUrl(): ?string {
    return $this->PaymentRedirectUrl;
  }



  /**
   * @param string $PaymentRedirectUrl
   * @return $this
   */
  public function setPaymentRedirectUrl($PaymentRedirectUrl) {
    $this->PaymentRedirectUrl = $PaymentRedirectUrl;

    return $this;
  }


}
