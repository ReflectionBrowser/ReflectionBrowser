<?php

final class RBClassesTemplateTable extends RBClassTemplateTable {
  protected function unitName() {
    return 'class';
  }

  protected function reflectOnName() {
    return 'classes';
  }

  protected function sourceDataForAllExtension() {
    return array_map(function($strClass) {
      return new ReflectionClass($strClass);
    }, get_declared_classes());
  }

  protected function sourceDataForExtension(ReflectionExtension $objExtension) {
    return array_filter($objExtension->getClasses(), function(ReflectionClass $objClass) {
      return !$objClass->isInterface() && !$objClass->isTrait();
    });
  }

  protected function inheritanceTypeFragmentForClass(ReflectionClass $objClass) {
    if ($objClass->getParentClass()) {
      return 'subclass';
    }

    return 'class';
  }

  protected function extraColumnsForClass(ReflectionClass $objClass) {
    return [
      RBReflectionClassExtensibilityGetFragment($objClass),
      $objClass->isInstantiable() ? 'instantiable' : new RBInactiveText('instantiable'),
      $objClass->isCloneable() ? 'cloneable' : new RBInactiveText('cloneable'),
    ];
  }
}
