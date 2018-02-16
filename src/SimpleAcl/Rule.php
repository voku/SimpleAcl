<?php

namespace SimpleAcl;

use SimpleAcl\Resource\ResourceAggregateInterface;
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
   * @var null|int
   */
  public $id;

  /**
   * Hold name of rule.
   *
   * @var string
   */
  public $name;

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
   * @param string $name
   */
  public function __construct($name)
  {
    $this->setId();
    $this->setName($name);
  }

  /**
   * Set aggregate objects.
   *
   * @param RoleAggregateInterface|mixed     $roleAggregate
   * @param ResourceAggregateInterface|mixed $resourceAggregate
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
  public function getResourceAggregate(): ResourceAggregateInterface
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
  public function getRoleAggregate(): RoleAggregateInterface
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
   * @return null|int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int|null $id
   */
  public function setId(int $id = null)
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
        !\is_callable($actionResult)
    ) {
      if (null !== $actionResult) {
        return (bool)$actionResult;
      }

      return null;
    }

    $actionResult = \call_user_func($this->action, $ruleResult);

    if (null !== $actionResult) {
      return (bool)$actionResult;
    }

    return null;
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
   * @param string|null $needRuleName
   * @param string|null $needRoleName
   * @param string|null $needResourceName
   *
   * @return RuleResult|null null is returned if there is no matched Role & Resource in this rule.
   *                         RuleResult otherwise.
   */
  public function isAllowed($needRuleName, $needRoleName, $needResourceName)
  {
    static $roleCache = [];
    static $resourceCache = [];

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
        $roles = [null];
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
        $resources = [null];
      }

      foreach ($roles as $role) {

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
            $ruleResult = new RuleResult($this, $needRoleName, $needResourceName);
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
   * @param string|null $needRuleName
   *
   * @return bool
   */
  protected function isRuleMatched($needRuleName): bool
  {
    return $this->name === $needRuleName;
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

  /**
   * @return Role|null
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
   * @return \SimpleAcl\Resource|null
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
   * Creates an id for rule.
   *
   * @return int
   */
  protected function generateId(): int
  {
    static $idCountRuleSimpleAcl = 1;

    return $idCountRuleSimpleAcl++;
  }
}
