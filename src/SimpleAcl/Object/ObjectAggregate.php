<?php

namespace SimpleAcl\Object;

use SimpleAcl\BaseObject;

/**
 * Implement common function for Role and Resources.
 *
 * @package SimpleAcl\Object
 */
abstract class ObjectAggregate
{
  /**
   * @var BaseObject[]
   */
  protected $objects = [];

  protected function removeObjects()
  {
    $this->objects = [];
  }

  /**
   * @param BaseObject|string $objectName
   *
   * @return bool
   */
  protected function removeObject($objectName)
  {
    if ($objectName instanceof BaseObject) {
      $objectName = $objectName->getName();
    }

    foreach ($this->objects as $objectIndex => $object) {
      if ($object->getName() === $objectName) {
        unset($this->objects[$objectIndex]);

        return true;
      }
    }

    return false;
  }

  /**
   * @return array|BaseObject[]
   */
  protected function getObjects()
  {
    return $this->objects;
  }

  /**
   * @param array $objects
   */
  protected function setObjects($objects)
  {
    /** @var \SimpleAcl\BaseObject $object */
    foreach ($objects as $object) {
      $this->addObject($object);
    }
  }

  /**
   * @param \SimpleAcl\BaseObject $object
   */
  protected function addObject(BaseObject $object)
  {
    if ($this->getObject($object)) {
      return;
    }

    $this->objects[] = $object;
  }

  /**
   * @param BaseObject|string $objectName
   *
   * @return null|BaseObject
   */
  protected function getObject($objectName)
  {
    if ($objectName instanceof BaseObject) {
      $objectName = $objectName->getName();
    }

    foreach ($this->objects as $object) {
      if ($object->getName() === $objectName) {
        return $object;
      }
    }

    return null;
  }

  /**
   * @return array
   */
  protected function getObjectNames(): array
  {
    $names = [];

    foreach ($this->objects as $object) {
      $names[] = $object->getName();
    }

    return $names;
  }
}
