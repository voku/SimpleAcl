<?php
namespace SimpleAcl;

use IteratorAggregate;

/**
 * Holds RuleResult sorted according priority.
 *
 * @package SimpleAcl
 */
class RuleResultCollection implements IteratorAggregate
{
  /**
   * @var array
   */
  private $collection;

  /**
   * __construct
   */
  public function __construct()
  {
    $this->collection = array();
  }

  /**
   * @return array
   */
  public function getIterator()
  {
    return new \ArrayIterator($this->collection);
  }

  /**
   * @param RuleResult $result
   */
  public function add(RuleResult $result = null)
  {
    if (!$result) {
      return;
    }

    if (null === $result->getAction()) {
      return;
    }

    $this->collection[] = $result;
  }

  /**
   * @return bool
   */
  public function get()
  {
    if (!$this->any()) {
      return false;
    }

    /** @var RuleResult $result */
    $result = array_pop($this->collection);

    return $result->getAction();
  }

  /**
   * @return bool
   */
  public function any()
  {
    return count($this->collection) > 0;
  }
}
