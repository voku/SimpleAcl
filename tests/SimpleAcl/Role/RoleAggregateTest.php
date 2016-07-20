<?php
namespace SimpleAclTest\Role;

use PHPUnit_Framework_TestCase;
use SimpleAcl\Role;
use SimpleAcl\Role\RoleAggregate;

/**
 * Class RoleAggregateTest
 *
 * @package SimpleAclTest\Role
 */
class RoleAggregateTest extends PHPUnit_Framework_TestCase
{
  public function testSetAndGetRoles()
  {
    $roles = array(new Role('One'), new Role('Tow'));

    $user = new RoleAggregate();

    self::assertSame(0, count($user->getRoles()));

    $user->setRoles($roles);

    self::assertSame($roles, $user->getRoles());

    self::assertSame(2, count($user->getRoles()));
  }

  public function testRoleAdd()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertSame(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertSame(2, count($user->getRoles()));

    self::assertSame(array($role1, $role2), $user->getRoles());
  }

  public function testGetRolesNames()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertSame(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertSame(2, count($user->getRoles()));

    self::assertSame(array('One', 'Tow'), $user->getRolesNames());
  }

  public function testRemoveRoles()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertSame(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertSame(2, count($user->getRoles()));

    $user->removeRoles();

    self::assertSame(0, count($user->getRoles()));

    self::assertNull($user->getRole('One'));
    self::assertNull($user->getRole('Tow'));
  }

  public function testRemoveRole()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertSame(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertSame(2, count($user->getRoles()));

    $user->removeRole('One');
    self::assertSame(1, count($user->getRoles()));
    self::assertSame($role2, $user->getRole('Tow'));

    $user->removeRole('UnDefinedTow');
    self::assertSame(1, count($user->getRoles()));

    $user->removeRole($role2);
    self::assertSame(0, count($user->getRoles()));
  }

  public function testAddRoleWithSameName()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('One');

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertSame(1, count($user->getRoles()));
    self::assertSame($role1, $user->getRole('One'));
  }
}
