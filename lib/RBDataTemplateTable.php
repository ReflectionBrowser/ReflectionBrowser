<?php

abstract class RBDataTemplateTable extends RBKeyTupleTemplateTable {
  private $objRuntime;

  public function __construct(RBRuntime $objRuntime) {
    parent::__construct();

    $this->objRuntime = $objRuntime;
    $mapData = $this->filterSourceData($this->sourceData());

    if (count($mapData) === 0) {
      $this->appendEmptyRow($this->emptyTableText());
    } else {
      $this->sortData($mapData);

      foreach ($mapData as $mixKey => $mixValue) {
        if (list($lstKeyColumns, $lstValueColumns) = $this->rowTupleForData($mixKey, $mixValue, $objRuntime)) {
          $this->appendTupleRow($lstKeyColumns, $lstValueColumns);
        }
      }
    }
  }

  abstract protected function sourceData();
  abstract protected function emptyTableText();
  protected function filterSourceData(array $mapData) { return $mapData; }
  protected function sortData(array &$mapData) {}
  abstract protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime);

  protected function linkedNameFragmentForClass(ReflectionClass $objClass, ReflectionClass $objCurrentClass) {
    $strName = $objClass->getName();

    if ($objClass == $objCurrentClass) {
      return $strName;
    }

    return RBHyperlink::forClassType($strName);
  }

  protected function manualFragmentForReflector(Reflector $objReflector) {
    if ($strURL = $this->objRuntime->manualURLForReflector($objReflector)) {
      return new RBHyperlink('manual', $strURL, true);
    }

    return '-';
  }

  protected function normalizeSymbol($strSymbol) {
    if (strpos($strSymbol, '\\') !== false && substr_count($strSymbol, '\\') === 1) {
      $strSymbol = substr($strSymbol, 1);
    }

    return $strSymbol;
  }

  protected function signatureFragmentForFunction(ReflectionFunctionAbstract $objFunction, $blnLink = true) {
    $strName = $objFunction->getName();
    $lstParameterSignatures = [];

    foreach ($objFunction->getParameters() as $objParameter) {
      $strParameterSignature = '';

      if ($objParameter->isOptional()) {
        $strParameterSignature .= '[';
      }

      if ($strTypeHint = $this->objRuntime->typeHintForParameter($objParameter)) {
        $strParameterSignature .= $strTypeHint;
        $strParameterSignature .= ' ';
      }

      if ($objParameter->isPassedByReference()) {
        $strParameterSignature .= '&';
      }

      if ($objParameter->isVariadic()) {
        $strParameterSignature .= '...';
      }

      $strParameterSignature .= '$';
      $strParameterSignature .= $objParameter->getName();

      if ($strDefaultValue = $this->objRuntime->defaultValueForParameter($objParameter)) {
        $strParameterSignature .= ' = ';
        $strParameterSignature .= $this->normalizeSymbol($strDefaultValue);
      }

      if ($objParameter->isOptional()) {
        $strParameterSignature .= ']';
      }

      $lstParameterSignatures[] = $strParameterSignature;
    }

    if ($objFunction instanceof ReflectionMethod) {
      $strFunctionName = $objFunction->getDeclaringClass()->getName();
      $strFunctionName .= RBReflectionMethodGetCallType($objFunction);
      $strFunctionName .= $strName;
    } else {
      $strFunctionName = $strName;
    }

    $lstSignatureParts = [
      $blnLink ? RBHyperlink::internalReflectOn('functions', $strName, $strFunctionName) : $strName,
      '(' . implode(', ', $lstParameterSignatures) . ')',
    ];

    if ($objFunction->returnsReference()) {
      array_unshift($lstSignatureParts, '&');
    }

    if ($objFunction->isGenerator()) {
      $lstSignatureParts[] = ': Generator';
    }

    return new RBXMLEscapedFragment($lstSignatureParts);
  }
}

trait RBDataTemplateTableMapSorter {
  protected function sortData(array &$mapData) {
    ksort($mapData, SORT_NATURAL | SORT_FLAG_CASE);
  }
}

trait RBDataTemplateTableListSorter {
  protected function sortData(array &$lstData) {
    sort($lstData, SORT_NATURAL | SORT_FLAG_CASE);
  }
}
