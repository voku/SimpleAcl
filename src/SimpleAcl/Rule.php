<?php
namespace SimpleAcl;

use SimpleAcl\Resource;
use SimpleAcl\Resource\ResourceAggregateInterface;
use SimpleAcl\Role;
use SimpleAcl\Role\RoleAggregateInterface;

/**
 * Used to connects Role and Resources together.
 *
 * @package SimpleAcl
 */
class Rule
{
  /**
   * Holds rule id.
   *
   * @var mixed
   */
  public $id;

  /**
   * Rule priority affect the order the rule is applied.
   *
   * @var int
   */
  protected $priority = 0;

  /**
   * Hold name of rule.
   *
   * @var string
   */
  protected $name;

  /**
   * Action used when determining is rule allow access to its Resource and Role.
   *
   * @var mixed
   */
  protected $action = false;

  /**
   * @var Role
   */
  protected $role;

  /**
   * @var \SimpleAcl\Resource
   */
  protected $resource;

  /**
   * @var RoleAggregateInterface
   */
  protected $roleAggregate;

  /**
   * @var ResourceAggregateInterface
   */
  protected $resourceAggregate;

  /**
   * Create Rule with given name.
   *
   * @param $name
   */
  public function __construct($name)
  {
    $this->setId();
    $this->setName($name);
  }

  /**
   * Set aggregate objects.
   *
   * @param $roleAggregate
   * @param $resourceAggregate
   */
  public function resetAggregate($roleAggregate, $resourceAggregate)
  {
    if ($roleAggregate instanceof RoleAggregateInterface) {
      $this->setRoleAggregate($roleAggregate);
    } else {
      $this->roleAggregate = null;
    }

    if ($resourceAggregate instanceof ResourceAggregateInterface) {
      $this->setResourceAggregate($resourceAggregate);
    } else {
      $this->resourceAggregate = null;
    }
  }

  /**
   * @return ResourceAggregateInterface
   */
  public function getResourceAggregate()
  {
    return $this->resourceAggregate;
  }

  /**
   * @param ResourceAggregateInterface $resourceAggregate
   */
  public function setResourceAggregate(ResourceAggregateInterface $resourceAggregate)
  {
    $this->resourceAggregate = $resourceAggregate;
  }

  /**
   * @return RoleAggregateInterface
   */
  public function getRoleAggregate()
  {
    return $this->roleAggregate;
  }

  /**
   * @param RoleAggregateInterface $roleAggregate
   */
  public function setRoleAggregate(RoleAggregateInterface $roleAggregate)
  {
    $this->roleAggregate = $roleAggregate;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id = null)
  {
    if (null === $id) {
      $id = $this->generateId();
    }

    $this->id = $id;
  }

  /**
   * @param RuleResult|null $ruleResult
   *
   * @return bool|null
   */
  public function getAction(RuleResult $ruleResult = null)
  {
    $actionResult = $this->action;

    if (
        null === $ruleResult
        ||
        !is_callable($actionResult)
    ) {
      if (null !== $actionResult) {
        return (bool)$actionResult;
      } else {
        return null;
      }
    }

    $actionResult = call_user_func($this->action, $ruleResult);

    if (null !== $actionResult) {
      return (bool)$actionResult;
    } else {
      return null;
    }
  }

  /**
   * @param mixed $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }

  /**
   * Check owing Role & Resource and match its with $roleName & $resourceName;
   * if match was found depending on action allow or deny access to $resourceName for $roleName.
   *
   * @param        $needRuleName
   * @param string $needRoleName
   * @param string $needResourceName
   *
   * @return RuleResult|null null is returned if there is no matched Role & Resource in this rule.
   *                         RuleResult otherwise.
   */
  public function isAllowed($needRuleName, $needRoleName, $needResourceName)
  {
    static $roleCache = array();
    static $resourceCache = array();

    if (
        (
            $needRuleName != 'RuleWide'
            &&
            $this->name === $needRuleName
        )
        ||
        $this->isRuleMatched($needRuleName)
    ) {

      if (null !== $this->role) {

        $roleNameTmp = $this->role->getName();

        if (!isset($roleCache[$roleNameTmp])) {
          $roles = iterator_to_array($this->role);

          $roleCache[$roleNameTmp] = $roles;
        } else {
          $roles = $roleCache[$roleNameTmp];
        }

      } else {
        $roles = array(null);
      }

      if (null !== $this->resource) {

        $resourceNameTmp = $this->resource->getName();

        if (!isset($resourceCache[$resourceNameTmp])) {
          $resources = iterator_to_array($this->resource);

          $resourceCache[$resourceNameTmp] = $resources;
        } else {
          $resources = $resourceCache[$resourceNameTmp];
        }

      } else {
        $resources = array(null);
      }

      $roleDepth = 0;
      $resourceDepth = 0;

      foreach ($roles as $role) {
        $roleDepth = $role ? $roleDepth + 1 : 0;

        if (
            null === $role
            ||
            ($role && $role->name === $needRoleName)
        ) {
          $roleNameMatched = true;
        } else {
          $roleNameMatched = false;
        }

        foreach ($resources as $resource) {
          $resourceDepth = $resource ? $resourceDepth + 1 : 0;
          $depth = $roleDepth + $resourceDepth;

          if (
              null === $resource
              ||
              ($resource && $resource->name === $needResourceName)
          ) {
            $resourceNameMatched = true;
          } else {
            $resourceNameMatched = false;
          }

          // Check if $role and $resource match to need role and resource.
          $ruleResult = null;
          if (
              $roleNameMatched === true
              &&
              $resourceNameMatched === true
          ) {
            $ruleResult = new RuleResult($this, -$depth, $needRoleName, $needResourceName);
          }

          if ($ruleResult) {
            return $ruleResult;
          }
        }
      }
    }

    return null;
  }

  /**
   * Check if rule can be used.
   *
   * @param $needRuleName
   *
   * @return bool
   */
  protected function isRuleMatched($needRuleName)
  {
    return $this->name === $needRuleName;
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

  /**
   * @return Role
   */
  public function getRole()
  {
    return $this->role;
  }

  /**
   * @param Role|null $role
   */
  public function setRole(Role $role = null)
  {
    $this->role = $role;
  }

  /**
   * @return \SimpleAcl\Resource
   */
  public function getResource()
  {
    return $this->resource;
  }

  /**
   * @param \SimpleAcl\Resource $resource
   */
  public function setResource(\SimpleAcl\Resource $resource = null)
  {
    $this->resource = $resource;
  }

  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }

  /**
   * Creates an id for rule.
   *
   * @return string
   */
  protected function generateId()
  {
    static $idCountRuleSimpleAcl = 1;

    return $idCountRuleSimpleAcl++;
  }
}
