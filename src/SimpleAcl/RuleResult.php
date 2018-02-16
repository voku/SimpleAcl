<?php

namespace SimpleAcl;

use SimpleAcl\Resource\ResourceAggregateInterface;
use SimpleAcl\Role\RoleAggregateInterface;

/**
 * Returned as result of Rule::isAllowed
 *
 * @package SimpleAcl
 */
class RuleResult
{
  /**
   * @var Rule
   */
  protected $rule;

  /**
   * @var string
   */
  protected $needRoleName;

  /**
   * @var string
   */
  protected $needResourceName;

  /**
   * @var int
   */
  protected $id;

  /**
   * @var mixed
   */
  protected $action;

  /**
   * @var bool
   */
  protected $isInit = false;

  /**
   * @param Rule        $rule
   * @param string|null $needRoleName
   * @param string|null $needResourceName
   */
  public function __construct(Rule $rule, $needRoleName, $needResourceName)
  {
    static $idCountRuleResultSimpleAcl = 1;

    $this->id = $idCountRuleResultSimpleAcl++;
    $this->rule = $rule;
    $this->needRoleName = $needRoleName;
    $this->needResourceName = $needResourceName;
  }

  /**
   * @return string
   */
  public function getNeedResourceName(): string
  {
    return $this->needResourceName;
  }

  /**
   * @return string
   */
  public function getNeedRoleName(): string
  {
    return $this->needRoleName;
  }

  /**
   * @return Rule
   */
  public function getRule(): Rule
  {
    return $this->rule;
  }

  /**
   * @return mixed
   */
  public function getAction()
  {
    if (!$this->isInit) {
      $this->action = $this->getRule()->getAction($this);
      $this->isInit = true;
    }

    return $this->action;
  }

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @return ResourceAggregateInterface
   */
  public function getResourceAggregate(): ResourceAggregateInterface
  {
    return $this->getRule()->getResourceAggregate();
  }

  /**
   * @return RoleAggregateInterface
   */
  public function getRoleAggregate(): RoleAggregateInterface
  {
    return $this->getRule()->getRoleAggregate();
  }
}
