<?php

final class RBTraitsTemplateTable extends RBClassTemplateTable {
  protected function unitName() {
    return 'trait';
  }

  protected function reflectOnName() {
    return 'traits';
  }

  protected function sourceDataForAllExtension() {
    return array_map(function($strClass) {
      return new ReflectionClass($strClass);
    }, get_declared_traits());
  }

  protected function sourceDataForExtension(ReflectionExtension $objExtension) {
    return array_filter($objExtension->getClasses(), function(ReflectionClass $objClass) {
      return $objClass->isTrait();
    });
  }

  protected function inheritanceTypeFragmentForClass(ReflectionClass $objClass) {
    return $objClass->getTraits() ? 'composed' : new RBInactiveText('composed');
  }
}
