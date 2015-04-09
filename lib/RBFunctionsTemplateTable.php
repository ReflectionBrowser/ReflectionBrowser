<?php

final class RBFunctionsTemplateTable extends RBExtensionDataTemplateTable {
  use RBDataTemplateTableListSorter;

  protected function emptyTableText() {
    return 'no functions';
  }

  protected function filterSourceData(array $mapData) {
    return array_filter($mapData, function($strFunction) {
      return !RBReflectorIsReflectionBrowserSymbol(new ReflectionFunction($strFunction));
    });
  }

  protected function sourceDataForAllExtension() {
    $mapFunctions = get_defined_functions();
    $lstFunctions = array_merge($mapFunctions['internal'], $mapFunctions['user']);
    return $lstFunctions;
  }

  protected function sourceDataForExtensionName($strExtensionName) {
    if ($lstFunctions = get_extension_funcs(strtolower($strExtensionName))) {
      return $lstFunctions;
    }

    return [];
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    $objFunction = new ReflectionFunction($mixValue);
    $lstColumns = [];

    if (!$this->isSingleExtensionTable() && $frgExtensionName = $objRuntime->sourceLocationFragmentForReflector($objFunction)) {
      $lstColumns[] = $frgExtensionName;
    }

    $lstColumns[] = $this->manualFragmentForReflector($objFunction);
    return [
      [$this->signatureFragmentForFunction($objFunction, $objRuntime)],
      $lstColumns
    ];
  }
}
