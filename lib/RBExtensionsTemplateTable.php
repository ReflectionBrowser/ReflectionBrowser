<?php

final class RBExtensionsTemplateTable extends RBDataTemplateTable {
  use RBDataTemplateTableListSorter;

  protected function sourceData() {
    return get_loaded_extensions();
  }

  protected function emptyTableText() {
    return 'no extensions';
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    $objExtension = new ReflectionExtension($mixValue);

    $lstColumns = [$objExtension->getVersion()];

    if ($lstExtraColumns = $objRuntime->extraInfoMapForExtension($objExtension)) {
      $lstColumns = array_merge($lstColumns, $lstExtraColumns);
    }

    $lstColumns[] = $this->manualFragmentForReflector($objExtension);

    return [
      [$objRuntime->nameFragmentForExtensionName($mixValue)],
      $lstColumns
    ];
  }
}
