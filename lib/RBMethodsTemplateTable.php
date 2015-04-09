<?php

final class RBMethodsTemplateTable extends RBReflectionClassDataTemplateTable {
  use RBDataTemplateTableListSorter;

  private $mapTraitAliases;

  protected function emptyTableText() {
    return 'no methods';
  }

  protected function sortData(array &$lstData) {
    usort($lstData, function(ReflectionMethod $objClassLeft, ReflectionMethod $objClassRight) {
      return strnatcasecmp($objClassLeft->getName(), $objClassRight->getName());
    });
  }

  protected function sourceDataForReflectionClass(ReflectionClass $objClass) {
    $this->mapTraitAliases = $objClass->getTraitAliases();
    return $objClass->getMethods();
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    return [
      [
        $this->linkedNameFragmentForClass($mixValue->getDeclaringClass(), $this->currentReflectionClass()),
        $this->signatureFragmentForMethod($mixValue),
      ],
      [
        $mixValue->isStatic() ? 'static' : new RBInactiveText('static'),
        RBReflectorGetVisibilty($mixValue),
        $this->prototypeFragmentForMethod($mixValue),
        $this->manualFragmentForReflector($mixValue),
      ]
    ];
  }

  protected function signatureFragmentForMethod(ReflectionMethod $objMethod) {
    $strMethodName = $objMethod->getName();

    if (isset($this->mapTraitAliases[$strMethodName])) {
      $objFragment = $this->signatureFragmentForFunction($objMethod, false);

      $strTraitMethodName = $this->mapTraitAliases[$strMethodName];
      $objMethod = RBReflectionMethodFromStaticName($strTraitMethodName);

      return new RBXMLEscapedFragment([
        $objFragment,
        RBXMLTag::emptyTag('br'),
        new RBXMLTag('i', ['style' => 'font-weight: normal;'], [
          '(alias of ',
          RBHyperlink::forMethod($objMethod),
          ')'
        ]),
      ]);
    }

    return $this->signatureFragmentForFunction($objMethod);
  }

  protected function prototypeFragmentForMethod(ReflectionMethod $objMethod) {
    try {
      $strNamePrototype = RBReflectionMethodGetName($objMethod->getPrototype());
      return new RBXMLTag('span', ['title' => $strNamePrototype], ['prototype']);
    } catch (ReflectionException $objException) {}

    return new RBInactiveText('prototype');
  }
}
