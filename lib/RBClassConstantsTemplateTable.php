<?php

final class RBClassConstantsTemplateTable extends RBReflectorDataTemplateTable {
  use RBDataTemplateTableMapSorter;

  protected function emptyTableText() {
    return 'no constants';
  }

  protected function sourceDataForReflector(Reflector $objClass) {
    return $objClass->getConstants();
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    return [
      [$mixKey],
      [new RBDumpedEscapedVariableValue($mixValue, true)]
    ];
  }
}
