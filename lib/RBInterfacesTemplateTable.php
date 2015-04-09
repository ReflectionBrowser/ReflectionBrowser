<?php

final class RBInterfacesTemplateTable extends RBClassTemplateTable {
  protected function unitName() {
    return 'interface';
  }

  protected function reflectOnName() {
    return 'interfaces';
  }

  protected function sourceDataForAllExtension() {
    return array_map(function($strClass) {
      return new ReflectionClass($strClass);
    }, get_declared_interfaces());
  }

  protected function sourceDataForExtension(ReflectionExtension $objExtension) {
    return array_filter($objExtension->getClasses(), function(ReflectionClass $objClass) {
      return $objClass->isInterface();
    });
  }

  protected function inheritanceTypeFragmentForClass(ReflectionClass $objClass) {
    if ($objClass->getInterfaces()) {
      return 'subinterface';
    }

    return 'interface';
  }
}
