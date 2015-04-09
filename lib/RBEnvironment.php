<?php

final class RBEnvironment {
  private $mapENV;

  public function __construct($mapENV) {
    $this->mapENV = $mapENV;
  }

  public function runtime() {
    if (isset($this->mapENV['HHVM'])) {
      require_once(__DIR__ . '/../runtimes/hhvm.php');
      return new RBHHVMRuntime();
    }

    require_once(__DIR__ . '/../runtimes/zend.php');
    return new RBZendRuntime();
  }
}
