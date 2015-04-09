<?php

final class RBExtensionDependenciesTemplateTable extends RBExtensionDataTemplateTable {
  use RBDataTemplateTableMapSorter;

  protected function emptyTableText() {
    return 'no dependencies';
  }

  protected function sourceDataForAllExtension() {
    return [];
  }

  protected function sourceDataForExtension(ReflectionExtension $objExtension) {
    return $objExtension->getDependencies();
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    return [
      [$mixKey],
      [$mixValue]
    ];
  }
}
