<?php

namespace SimpleAcl;

use IteratorAggregate;
use SimpleAcl\Object\RecursiveIterator;

/**
 * Use to keep shared function between Roles and Resources.
 *
 * @package SimpleAcl
 */
abstract class BaseObject implements IteratorAggregate
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
  public function __construct(string $name)
  {
    $this->setName($name);
  }

  /**
   * @return RecursiveIterator
   */
  public function getIterator(): RecursiveIterator
  {
    return new RecursiveIterator([$this]);
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name)
  {
    $this->name = $name;
  }
}
