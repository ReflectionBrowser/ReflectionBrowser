<?php

abstract class RBExtensionDataTemplateTable extends RBDataTemplateTable {
  private $strExtensionName;

  final public function __construct(RBRuntime $objRuntime, $strExtensionName = null) {
    $this->strExtensionName = $strExtensionName;
    parent::__construct($objRuntime);
  }

  final public function sourceData() {
    if ($this->strExtensionName === null) {
      return $this->sourceDataForAllExtension();
    }

    return $this->sourceDataForExtensionName($this->strExtensionName);
  }

  final protected function isSingleExtensionTable() {
    return $this->strExtensionName !== null;
  }

  abstract protected function sourceDataForAllExtension();
  protected function sourceDataForExtensionName($strExtensionName) {
    return $this->sourceDataForExtension(new ReflectionExtension($strExtensionName));
  }

  protected function sourceDataForExtension(ReflectionExtension $objExtension) {
    return null;
  }
}
