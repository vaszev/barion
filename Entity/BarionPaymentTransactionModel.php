<?php

namespace Vaszev\BarionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vaszev\BarionBundle\Service\Currency;


/**
 * @ORM\Entity(repositoryClass="Vaszev\BarionBundle\Repository\BarionPaymentTransactionModelRepository")
 */
class BarionPaymentTransactionModel extends Base {

  /**
   * @var string
   * @ORM\Column(name="payee", type="string", length=255)
   * @Assert\Email()
   */
  private $Payee;

  /**
   * @var float
   * @Assert\GreaterThan(value="0")
   * @ORM\Column(name="total", type="decimal", precision=17, scale=2, nullable=true)
   */
  private $Total = null;

  /**
   * @var string
   * @ORM\Column(name="currency", type="string", length=255)
   */
  private $Currency = Currency::HUF;

  /**
   * @var string
   * @ORM\Column(name="comment", type="text", length=1000, nullable=true)
   */
  private $Comment = null;

  /**
   * @ORM\OneToMany(targetEntity="Vaszev\BarionBundle\Entity\BarionItemModel", mappedBy="Transaction", cascade={"persist"})
   * @Assert\Count(min=1)
   */
  private $Items;

  /**
   * @ORM\ManyToOne(targetEntity="Vaszev\BarionBundle\Entity\BarionPaymentRequestModel", inversedBy="Transactions", cascade={"persist"})
   * @Assert\NotNull()
   */
  private $RequestModel;

  /**
   * Using for B2B or C2C, currently not supported
   * I.e. Immediately after crediting €20 to firstfarmer@example.com, €2 will be sent to the marketplace and €2 will be sent to the local agent.
   */
  private $PayeeTransactions;



  /**
   * BarionPaymentTransactionModel constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->Items = new ArrayCollection();
  }



  /**
   * @return string
   */
  public function getPayee(): ?string {
    return $this->Payee;
  }



  /**
   * @param string $Payee
   * @return $this
   */
  public function setPayee($Payee) {
    $this->Payee = $Payee;

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
  public function getComment(): ?string {
    return $this->Comment;
  }



  /**
   * @param string $Comment
   * @return $this
   */
  public function setComment($Comment) {
    $this->Comment = $Comment;

    return $this;
  }



  /**
   * @return mixed
   */
  public function getItems() {
    return $this->Items;
  }



  /**
   * @param BarionItemModel[] $Items
   * @return $this
   */
  public function setItems($Items) {
    foreach ($Items as $item) {
      $this->addItem($item);
    }

    return $this;
  }



  /**
   * @param BarionItemModel $Item
   * @return $this
   */
  public function addItem($Item) {
    if (!$this->Items->contains($Item)) {
      $Item->setTransaction($this);
      $this->Items->add($Item);
      $this->setTotal($this->getTotal() + $Item->getItemTotal());
    }

    return $this;
  }



  /**
   * @return mixed
   */
  public function getRequestModel() {
    return $this->RequestModel;
  }



  /**
   * @param mixed $RequestModel
   * @return $this
   */
  public function setRequestModel($RequestModel) {
    $this->RequestModel = $RequestModel;

    return $this;
  }


}
