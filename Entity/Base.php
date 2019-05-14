<?php

namespace Vaszev\BarionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Vaszev\BarionBundle\Controller\BarionController;

/**
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
class Base {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  protected $created;

  /**
   * @ORM\Column(type="datetime")
   */
  protected $edited;

  /**
   * @var boolean
   * @ORM\Column(name="deleted", type="boolean")
   */
  private $deleted = false;



  /**
   * Base constructor.
   */
  public function __construct() {
    if ($this->isNew()) {
      try {
        $this->created = $this->edited = new \DateTime();
      } catch (\Exception $e) {
        // error
      }
    }
  }



  /**
   * @return bool
   */
  public function isDeleted(): bool {
    return $this->deleted;
  }



  /**
   * @param bool $deleted
   * @return $this
   */
  public function setDeleted($deleted) {
    $this->deleted = $deleted;

    return $this;
  }



  /**
   * Get the entity id
   * @return int
   */
  public function getId() {
    return ( int )$this->id;
  }



  /**
   * Get the time of creation
   * @return \DateTime
   */
  public function getCreated() {
    return $this->created;
  }



  /**
   * Get the time of edition
   * @return \DateTime
   */
  public function getEdited() {
    return $this->edited;
  }



  /**
   * Check if the entity is new
   * @return bool
   */
  public function isNew() {
    return (empty ($this->id));
  }



  /**
   * Set the time of edition
   * @ORM\PreUpdate
   */
  public function updateEdited() {
    try {
      $this->edited = new \DateTime();
    } catch (\Exception $e) {
      // error
    }
  }



  /**
   * Bind data
   * @param array $array
   * @return Base
   * @throws \ReflectionException
   */
  public function bind($array) {
    $ref = new \ReflectionClass ($this);
    $pros = $ref->getDefaultProperties();
    foreach (array_intersect_key($array, $pros) as $key => $value) {
      if (is_string($value)) {
        $value = trim($value);
      }
      $method = "set" . ucfirst($key);
      $ref->hasMethod($method) ? $this->{$method} ($value) : $this->{$key} = $value;
    }

    return $this;
  }



  /**
   * @param null $txt
   * @param string $defLocale
   * @param string $domain
   * @return string|null
   */
  protected function translate($txt = null, $defLocale = 'hu', $domain = 'messages') {
    if (empty(trim($txt))) {
      return null;
    }
    $locale = BarionController::getLocale($defLocale);
    $translator = new Translator($locale);
    $translator->addLoader('yaml', new YamlFileLoader());
    $translator->addResource('yaml', __DIR__ . '/../../translations/messages.' . strtolower($locale) . '.yml', $locale);
    $txt = $translator->trans($txt, [], $domain);

    return (string)$txt;
  }
}
