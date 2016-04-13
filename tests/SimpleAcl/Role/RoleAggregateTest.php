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

    self::assertEquals(0, count($user->getRoles()));

    $user->setRoles($roles);

    self::assertEquals($roles, $user->getRoles());

    self::assertEquals(2, count($user->getRoles()));
  }

  public function testRoleAdd()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertEquals(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertEquals(2, count($user->getRoles()));

    self::assertEquals(array($role1, $role2), $user->getRoles());
  }

  public function testGetRolesNames()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertEquals(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertEquals(2, count($user->getRoles()));

    self::assertSame(array('One', 'Tow'), $user->getRolesNames());
  }

  public function testRemoveRoles()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertEquals(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertEquals(2, count($user->getRoles()));

    $user->removeRoles();

    self::assertEquals(0, count($user->getRoles()));

    self::assertNull($user->getRole('One'));
    self::assertNull($user->getRole('Tow'));
  }

  public function testRemoveRole()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('Tow');

    self::assertEquals(0, count($user->getRoles()));

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertEquals(2, count($user->getRoles()));

    $user->removeRole('One');
    self::assertEquals(1, count($user->getRoles()));
    self::assertEquals($role2, $user->getRole('Tow'));

    $user->removeRole('UnDefinedTow');
    self::assertEquals(1, count($user->getRoles()));

    $user->removeRole($role2);
    self::assertEquals(0, count($user->getRoles()));
  }

  public function testAddRoleWithSameName()
  {
    $user = new RoleAggregate();

    $role1 = new Role('One');
    $role2 = new Role('One');

    $user->addRole($role1);
    $user->addRole($role2);

    self::assertEquals(1, count($user->getRoles()));
    self::assertSame($role1, $user->getRole('One'));
  }
}