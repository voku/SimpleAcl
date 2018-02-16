<?php

namespace SimpleAclTest\Role;

use PHPUnit\Framework\TestCase;
use SimpleAcl\Role;
use SimpleAcl\Role\RoleAggregate;

/**
 * Class RoleAggregateTest
 *
 * @package SimpleAclTest\Role
 */
class RoleAggregateTest extends TestCase
{
  public function testSetAndGetRoles()
  {
    $roles = [new Role('One'), new Role('Tow')];

    $user = new RoleAggregate();

    self::assertCount(0, $user->getRoles());

    $user->setRoles($roles);

    self::assertSame($roles, $user->getRoles());

    self::assertCount(2, $user->getRoles());
  }

  public function testRoleAdd()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertCount(0, $user->getRoles());

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertCount(2, $user->getRoles());

    self::assertSame([$role1, $role2], $user->getRoles());
  }

  public function testGetRolesNames()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertCount(0, $user->getRoles());

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertCount(2, $user->getRoles());

    self::assertSame(['One', 'Tow'], $user->getRolesNames());
  }

  public function testRemoveRoles()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertCount(0, $user->getRoles());

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertCount(2, $user->getRoles());

    $user->removeRoles();

    self::assertCount(0, $user->getRoles());

    self::assertNull($user->getRole('One'));
    self::assertNull($user->getRole('Tow'));
  }

  public function testRemoveRole()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertCount(0, $user->getRoles());

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertCount(2, $user->getRoles());

    $user->removeRole('One');
    self::assertCount(1, $user->getRoles());
    self::assertSame($role2, $user->getRole('Tow'));

    $user->removeRole('UnDefinedTow');
    self::assertCount(1, $user->getRoles());

    $user->removeRole($role2);
    self::assertCount(0, $user->getRoles());
  }

  public function testAddRoleWithSameName()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('One');

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertCount(1, $user->getRoles());
    self::assertSame($role1, $user->getRole('One'));
  }
}
