<?php

abstract class RBReflectionClassDataTemplateTable extends RBReflectorDataTemplateTable {
  private $objClass;

  final protected function sourceDataForReflector(Reflector $objClass) {
    $this->objClass = $objClass;
    return $this->sourceDataForReflectionClass($objClass);
  }

  abstract protected function sourceDataForReflectionClass(ReflectionClass $objClass);

  final protected function currentReflectionClass() {
    return $this->objClass;
  }
}
