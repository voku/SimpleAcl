<?php

namespace SimpleAcl;

use SplPriorityQueue as Base;

/**
 * Class SplPriorityQueue
 *
 * @package SimpleAcl
 */
class SplPriorityQueue extends Base
{
  /**
   * @var int
   */
  protected $queueOrder = 0;

  /**
   * insert
   *
   * @param mixed $datum
   * @param int   $rulePriority
   */
  public function insert($datum, $rulePriority = 0)
  {
    parent::insert($datum, $rulePriority);
  }
}
