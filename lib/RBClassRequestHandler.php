<?php

abstract class RBClassRequestHandler extends RBMasterDetailRequestHandler {
  protected function processMasterRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $strClassName = $this->masterTemplateClass();

    $objRootFragment->appendContentFragments([
      new RBPageTitle($this->masterName()),
      new $strClassName($this->runtime()),
    ]);
  }

  protected function processDetailRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $strName = $objRequest->queryParameterForKey('name');
    $objRuntime = $this->runtime();
    $objClass = new ReflectionClass($strName);

    $frgManual = null;
    if ($objClass->isInternal()) {
      $frgManual = new RBHyperlink('manual', $objRuntime->manualURLForIdentifier($strName), true);
    }

    $objRootFragment->appendContentFragments([
      new RBPageTitle($strName, $frgManual),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBKeyValueTemplateTable([
            'Source location' => $objRuntime->longSourceLocationFragmentForReflector($objClass),
            'Inheritance tree' => $this->inheritanceTreeFragmentForClass($objClass),
          ]),
          new RBKeyValueTemplateTable([
            'Extensibility' => RBReflectionClassExtensibilityGetFragment($objClass),
            'Instantiable' => $objClass->isInstantiable() ? 'Yes' : 'No',
            'Clonable' => $objClass->isCloneable() ? 'Yes' : 'No',
            'Iterator' => $objClass->isIterateable() ? 'Yes' : 'No',
          ]),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Methods']),
          new RBMethodsTemplateTable($objRuntime, $objClass),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Properties']),
          new RBPropertiesTemplateTable($objRuntime, $objClass),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Constants']),
          new RBClassConstantsTemplateTable($objRuntime, $objClass),
        ]),
      ]),
    ]);
  }

  abstract protected function masterName();
  abstract protected function masterTemplateClass();

  private function inheritanceTreeFragmentForClass(ReflectionClass $objClass) {
    if ($frgList = $this->inheritanceListFragmentForClass($objClass)) {
      return new RBXMLEscapedFragment([
        new RBXMLTag('b', [], [$objClass->getName()]),
        $frgList,
      ]);
    }

    return new RBInactiveText('(no superclass, implemented interfaces or used traits)', 'i');
  }

  private function inheritanceListFragmentForClass(ReflectionClass $objClass) {
    $objParentClass = $objClass->getParentClass();
    $lstInterfaces = RBReflectionClassInheritanceGetInterfaces($objClass);
    $lstTraits = $objClass->getTraits();

    if ($objParentClass || $lstInterfaces || $lstTraits) {
      $objList = new RBXMLTag('ul');

      if ($objParentClass) {
        $objList->appendContentFragment($this->inheritanceSubtreeFragmentForClass($objParentClass));
      }

      if ($lstInterfaces) {
        $this->appendInterfacesToList($lstInterfaces, $objList);
      }

      if ($lstTraits) {
        $this->appendTraitsToList($lstTraits, $objList);
      }

      return $objList;
    }

    return null;
  }

  private function inheritanceSubtreeFragmentForClass(ReflectionClass $objClass) {
    $objFragment = new RBXMLTag('li', [], [
      'extends ',
      $this->linkedNameFragmentForClass($objClass),
    ]);

    if ($frgList = $this->inheritanceListFragmentForClass($objClass)) {
      $objFragment->appendContentFragment($frgList);
    }

    return $objFragment;
  }

  private function inheritanceSubtreeFragmentForInterface(ReflectionClass $objInterface) {
    $objFragment = new RBXMLTag('li', [], [
      'implements ',
      $this->linkedNameFragmentForInterface($objInterface),
    ]);

    if ($lstInterfaces = RBReflectionClassInheritanceGetInterfaces($objInterface)) {
      $objList = $objFragment->appendContentFragment(new RBXMLTag('ul'));
      $this->appendInterfacesToList($lstInterfaces, $objList);
    }

    return $objFragment;
  }

  private function inheritanceSubtreeFragmentForTrait(ReflectionClass $objTrait) {
    $objFragment = new RBXMLTag('li', [], [
      'uses ',
      $this->linkedNameFragmentForTrait($objTrait),
    ]);

    if ($lstTraits = $objTrait->getTraits()) {
      $objList = $objFragment->appendContentFragment(new RBXMLTag('ul'));
      $this->appendTraitsToList($lstTraits, $objList);
    }

    return $objFragment;
  }

  private function appendInterfacesToList(array $lstInterfaces, RBXMLTag $objList) {
    ksort($lstInterfaces, SORT_NATURAL | SORT_FLAG_CASE);
    foreach ($lstInterfaces as $objInterface) {
      $objList->appendContentFragment($this->inheritanceSubtreeFragmentForInterface($objInterface));
    }
  }

  private function appendTraitsToList(array $lstTraits, RBXMLTag $objList) {
    ksort($lstTraits, SORT_NATURAL | SORT_FLAG_CASE);
    foreach ($lstTraits as $objTrait) {
      $objList->appendContentFragment($this->inheritanceSubtreeFragmentForTrait($objTrait));
    }
  }

  private function linkedNameFragmentForClass(ReflectionClass $objClass) {
    $strName = $objClass->getName();
    return RBHyperlink::internalReflectOn('classes', $strName, $strName);
  }

  private function linkedNameFragmentForInterface(ReflectionClass $objInterface) {
    $strName = $objInterface->getName();
    return RBHyperlink::internalReflectOn('interfaces', $strName, $strName);
  }

  private function linkedNameFragmentForTrait(ReflectionClass $objTrait) {
    $strName = $objTrait->getName();
    return RBHyperlink::internalReflectOn('traits', $strName, $strName);
  }
}
