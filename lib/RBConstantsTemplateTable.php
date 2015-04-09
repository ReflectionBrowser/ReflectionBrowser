<?php

final class RBConstantsTemplateTable extends RBExtensionDataTemplateTable {
  use RBDataTemplateTableMapSorter;

  protected function emptyTableText() {
    return 'no constants';
  }

  protected function sourceDataForAllExtension() {
    $lstExtensions = array_keys(get_defined_constants(true));
    return $this->sourceDataForExtensionList($lstExtensions);
  }

  protected function sourceDataForExtensionName($strExtensionName) {
    return $this->sourceDataForExtensionList([$strExtensionName]);
  }

  private function sourceDataForExtensionList(array $lstExtensions) {
    $mapConstants = [];
    $mapConstantsArray = get_defined_constants(true);

    foreach ($lstExtensions as $strExtension) {
      if (!isset($mapConstantsArray[$strExtension]) || count($mapConstantsArray[$strExtension]) === 0) {
        continue;
      }

      foreach ($mapConstantsArray[$strExtension] as $strName => $mixValue) {
        $mapConstants[$strName] = [$strExtension, $mixValue];
      }
    }

    return $mapConstants;
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    if ($this->isSingleExtensionTable()) {
      return [
        [$mixKey],
        [new RBDumpedEscapedVariableValue($mixValue[1], true)]
      ];
    } else {
      return [
        [$mixKey],
        [
          $objRuntime->nameFragmentForConstantExtensionName($mixValue[0]),
          new RBDumpedEscapedVariableValue($mixValue[1], true),
        ]
      ];
    }
  }
}
