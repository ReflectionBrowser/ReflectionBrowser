<?php

final class RBParametersTemplateTable extends RBReflectorDataTemplateTable {
  protected function sourceDataForReflector(Reflector $objFunction) {
    return $objFunction->getParameters();
  }

  protected function emptyTableText() {
    return 'no parameters';
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    return [
      [$mixValue->getName()],
      [
        $mixValue->isOptional() ? 'optional' : 'required',
        $this->passSemanticsFragmentForParameter($mixValue),
        $objRuntime->typeHintFragmentForParameter($mixValue),
        $this->defaultValueFragmentForParameter($objRuntime, $mixValue),
      ]
    ];
  }

  private function passSemanticsFragmentForParameter(ReflectionParameter $objParameter) {
    return new RBXMLEscapedFragment([
      $objParameter->isPassedByReference() ? 'reference' : new RBInactiveText('reference'),
      ', ',
      $objParameter->canBePassedByValue() ? 'value' : new RBInactiveText('value'),
    ]);
  }

  private function defaultValueFragmentForParameter(RBRuntime $objRuntime, ReflectionParameter $objParameter) {
    $mixDefaultValue = $objRuntime->defaultValueForParameter($objParameter);

    if ($mixDefaultValue === null) {
      return new RBInactiveText('(no default value)', 'i');
    }

    if ($mixDefaultValue === false) {
      return new RBInactiveText('(no default available)', 'i');
    }

    if (defined($mixDefaultValue) && !in_array(strtolower($mixDefaultValue), ['null', 'false', 'true'])) {
      $mixDefaultValue = $this->normalizeSymbol($mixDefaultValue);
      $lstFragments = [];

      if ($intEndClassNamePosition = strpos($mixDefaultValue, '::')) {
        $strClassName = substr($mixDefaultValue, 0, $intEndClassNamePosition);
        $lstFragments[] = RBHyperlink::internalReflectOn('classes', $mixDefaultValue, $strClassName);
      } else {
        $lstFragments[] = RBHyperlink::internalReflectOn('constants', $mixDefaultValue);
      }

      $lstFragments[] = new RBDumpedEscapedVariableValue(constant($mixDefaultValue), true);

      return new RBXMLEscapedFragment($lstFragments);
    }

    return new RBVariableValue($mixDefaultValue, true);
  }
}
