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
   * @var string
   */
  protected $id;

  /**
   * @var
   */
  protected $action;

  /**
   * @var bool
   */
  protected $isInit = false;

  /**
   * @param Rule $rule
   * @param      $needRoleName
   * @param      $needResourceName
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
  public function getNeedResourceName()
  {
    return $this->needResourceName;
  }

  /**
   * @return string
   */
  public function getNeedRoleName()
  {
    return $this->needRoleName;
  }

  /**
   * @return Rule
   */
  public function getRule()
  {
    return $this->rule;
  }

  /**
   * @return bool
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
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return ResourceAggregateInterface
   */
  public function getResourceAggregate()
  {
    return $this->getRule()->getResourceAggregate();
  }

  /**
   * @return RoleAggregateInterface
   */
  public function getRoleAggregate()
  {
    return $this->getRule()->getRoleAggregate();
  }
}
