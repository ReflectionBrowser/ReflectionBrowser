<?php

final class RBPropertiesTemplateTable extends RBReflectionClassDataTemplateTable {
  use RBDataTemplateTableListSorter;

  private $mapDefaultValues;

  protected function sourceDataForReflectionClass(ReflectionClass $objClass) {
    $this->mapDefaultValues = $objClass->getDefaultProperties();
    return $objClass->getProperties();
  }

  protected function emptyTableText() {
    return 'no properties';
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    $strName = $mixValue->getName();
    return [
      [
        $this->linkedNameFragmentForClass($mixValue->getDeclaringClass(), $this->currentReflectionClass()),
        $strName,
      ],
      [
        $mixValue->isStatic() ? 'static' : new RBInactiveText('static'),
        RBReflectorGetVisibilty($mixValue),
        new RBDumpedEscapedVariableValue($this->mapDefaultValues[$strName], true),
      ]
    ];
  }
}
