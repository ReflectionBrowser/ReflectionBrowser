<?php

abstract class RBClassTemplateTable extends RBExtensionDataTemplateTable {
  protected function emptyTableText() {
    return sprintf('no %ss', $this->unitName());
  }

  protected function filterSourceData(array $mapData) {
    return array_filter($mapData, function($objClass) {
      return !RBReflectorIsReflectionBrowserSymbol($objClass);
    });
  }

  protected function sortData(array &$lstData) {
    usort($lstData, function(ReflectionClass $objClassLeft, ReflectionClass $objClassRight) {
      return strnatcasecmp($objClassLeft->getName(), $objClassRight->getName());
    });
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    $lstColumns = [];

    if (!$this->isSingleExtensionTable() && $frgExtensionName = $objRuntime->sourceLocationFragmentForReflector($mixValue)) {
      $lstColumns[] = $frgExtensionName;
    }

    $lstColumns[] = $this->inheritanceTypeFragmentForClass($mixValue);
    $lstColumns = array_merge($lstColumns, $this->extraColumnsForClass($mixValue));
    $lstColumns[] = $this->manualFragmentForReflector($mixValue);

    $strName = $mixValue->getName();
    return [
      [RBHyperlink::internalReflectOn($this->reflectOnName(), $strName, $strName)],
      $lstColumns
    ];
  }

  abstract protected function unitName();
  abstract protected function reflectOnName();
  abstract protected function inheritanceTypeFragmentForClass(ReflectionClass $objClass);
  protected function extraColumnsForClass(ReflectionClass $objClass) {
    return [];
  }
}
