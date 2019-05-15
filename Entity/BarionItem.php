<?php

namespace Vaszev\BarionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="Vaszev\BarionBundle\Repository\BarionItemRepository")
 */
class BarionItem extends Base {

  /**
   * @var string
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $Name;

  /**
   * @var string
   * @ORM\Column(name="description", type="text", length=500)
   * @Assert\NotBlank()
   */
  private $Description;

  /**
   * @var int
   * @ORM\Column(name="quantity", type="integer")
   * @Assert\GreaterThan(value="0")
   */
  private $Quantity;

  /**
   * @var string
   * @ORM\Column(name="unit", type="string", length=255)
   */
  private $Unit = "piece";

  /**
   * @var float
   * @Assert\GreaterThan(value="0")
   * @ORM\Column(name="unit_price", type="decimal", precision=17, scale=2)
   * @Assert\GreaterThan(value="0")
   */
  private $UnitPrice;

  /**
   * @var float
   * @Assert\GreaterThan(value="0")
   * @ORM\Column(name="item_total", type="decimal", precision=17, scale=2)
   * @Assert\GreaterThan(value="0")
   */
  private $ItemTotal;

  /**
   * @var string
   * @ORM\Column(name="sku", type="string", length=255)
   */
  private $SKU;

  /**
   * @ORM\ManyToOne(targetEntity="BarionPaymentTransaction", inversedBy="Items")
   * @Assert\NotNull()
   */
  private $Transaction;



  /**
   * @return mixed
   */
  public function getTransaction() {
    return $this->Transaction;
  }



  /**
   * @param mixed $Transaction
   * @return $this
   */
  public function setTransaction($Transaction) {
    $this->Transaction = $Transaction;

    return $this;
  }



  /**
   * @return string
   */
  public function getName(): ?string {
    return $this->Name;
  }



  /**
   * @param string $Name
   * @return $this
   */
  public function setName($Name) {
    $this->Name = $Name;

    return $this;
  }



  /**
   * @return string
   */
  public function getDescription(): ?string {
    return $this->Description;
  }



  /**
   * @param string $Description
   * @return $this
   */
  public function setDescription($Description) {
    $this->Description = $Description;

    return $this;
  }



  /**
   * @return int
   */
  public function getQuantity(): ?int {
    return $this->Quantity;
  }



  /**
   * @param int $Quantity
   * @return $this
   */
  public function setQuantity($Quantity) {
    $this->Quantity = $Quantity;

    return $this;
  }



  /**
   * @return string
   */
  public function getUnit(): ?string {
    return $this->Unit;
  }



  /**
   * @param string $Unit
   * @return $this
   */
  public function setUnit($Unit) {
    $this->Unit = $Unit;

    return $this;
  }



  /**
   * @return float
   */
  public function getUnitPrice(): ?float {
    return $this->UnitPrice;
  }



  /**
   * @param float $UnitPrice
   * @return $this
   */
  public function setUnitPrice($UnitPrice) {
    $this->UnitPrice = $UnitPrice;

    return $this;
  }



  /**
   * @return float
   */
  public function getItemTotal(): ?float {
    return $this->ItemTotal;
  }



  /**
   * @param float $ItemTotal
   * @return $this
   */
  public function setItemTotal($ItemTotal) {
    $this->ItemTotal = $ItemTotal;

    return $this;
  }



  /**
   * @return string
   */
  public function getSKU(): ?string {
    return $this->SKU;
  }



  /**
   * @param string $SKU
   * @return $this
   */
  public function setSKU($SKU) {
    $this->SKU = $SKU;

    return $this;
  }


}
