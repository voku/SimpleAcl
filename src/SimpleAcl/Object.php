<?php
namespace SimpleAcl;

use IteratorAggregate;
use SimpleAcl\Object\RecursiveIterator;

/**
 * Use to keep shared function between Roles and Resources.
 *
 * @package SimpleAcl
 */
abstract class Object implements IteratorAggregate
{
  /**
   * Hold the name of object.
   *
   * @var string
   */
  public $name;

  /**
   * Create Object with given name.
   *
   * @param string $name
   */
  public function __construct($name)
  {
    $this->setName($name);
  }

  /**
   * @return RecursiveIterator
   */
  public function getIterator()
  {
    return new RecursiveIterator(array($this));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
}
