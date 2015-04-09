<?php

abstract class RBReflectorDataTemplateTable extends RBDataTemplateTable {
  private $objReflector;

  final public function __construct(RBRuntime $objRuntime, Reflector $objReflector) {
    $this->objReflector = $objReflector;
    parent::__construct($objRuntime);
  }

  final public function sourceData() {
    return $this->sourceDataForReflector($this->objReflector);
  }

  abstract protected function sourceDataForReflector(Reflector $objReflector);
}
