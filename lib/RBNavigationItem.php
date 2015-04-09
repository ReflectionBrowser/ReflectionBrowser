<?php

class RBNavigationItem {
  private $strDescription;
  private $strIconName;

  public function __construct($strDescription, $strIconName) {
    $this->strDescription = $strDescription;
    $this->strIconName = $strIconName;
  }

  public function description() {
    return $this->strDescription;
  }

  public function iconName() {
    return $this->strIconName;
  }
}

final class RBDividerNavigationItem extends RBNavigationItem {
  public function __construct() {
    parent::__construct(null, null);
  }

  public function description() {
    throw new LogicException('A divider navigation item has no description');
  }

  public function iconName() {
    throw new LogicException('A divider navigation item has no icon name');
  }
}

final class RBReflectOnNavigationItem extends RBNavigationItem {
  private $strReflectOn;

  public function __construct($strReflectOn, $strDescription, $strIconName) {
    parent::__construct($strDescription, $strIconName);
    $this->strReflectOn = $strReflectOn;
  }

  public function reflectOn() {
    return $this->strReflectOn;
  }
}
