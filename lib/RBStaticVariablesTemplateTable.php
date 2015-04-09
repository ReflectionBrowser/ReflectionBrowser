<?php

final class RBStaticVariablesTemplateTable extends RBReflectorDataTemplateTable {
  use RBDataTemplateTableMapSorter;

  protected function sourceDataForReflector(Reflector $objFunction) {
    return $objFunction->getStaticVariables();
  }

  protected function emptyTableText() {
    return 'no static variables';
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    return [
      [$mixKey],
      [new RBDumpedVariableValue($mixValue, true)]
    ];
  }
}
