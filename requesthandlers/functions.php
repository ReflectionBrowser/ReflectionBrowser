<?php

final class RBFunctionsRequestHandler extends RBMasterDetailRequestHandler {
  protected function processMasterRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $objRootFragment->appendContentFragments([
      new RBPageTitle('Functions'),
      new RBFunctionsTemplateTable($this->runtime()),
    ]);
  }

  protected function processDetailRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $strName = $objRequest->queryParameterForKey('name');
    $objRuntime = $this->runtime();

    if (strpos($strName, '::') !== false) {
      $objFunction = RBReflectionMethodFromStaticMethodName($strName);
    } else if (strpos($strName, '->') !== false) {
      list($strClassName, $strMethodName) = explode('->', $strName);
      $objFunction = new ReflectionMethod($strClassName, $strMethodName);
    } else {
      $objFunction = new ReflectionFunction($strName);
    }

    $frgManual = null;
    if ($objFunction->isInternal()) {
      $frgManual = new RBHyperlink('manual', $objRuntime->manualURLForReflector($objFunction), true);
    }

    $mapInfo = ['Source location' => $objRuntime->longSourceLocationFragmentForReflector($objFunction)];
    if ($objFunction instanceof ReflectionMethod) {
      $mapInfo['Inheritance tree'] = $this->inheritanceTreeFragmentForMethod($objFunction);
    }

    $objRootFragment->appendContentFragments([
      new RBPageTitle($strName, $frgManual),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBKeyValueTemplateTable($mapInfo),
          new RBKeyValueTemplateTable([
            'Deprecated' => $objFunction->isDeprecated() ? new RBXMLTag('b', ['style' => 'color: red;'], ['Yes']) : 'No',
            'Generator' => $objFunction->isGenerator() ? 'Yes' : 'No',
            'Variadic' => $objFunction->isVariadic() ? 'Yes' : 'No',
          ]),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Parameters']),
          new RBParametersTemplateTable($objRuntime, $objFunction),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Static variables']),
          new RBStaticVariablesTemplateTable($objRuntime, $objFunction),
        ]),
      ]),
    ]);
  }

  private function inheritanceTreeFragmentForMethod(ReflectionMethod $objMethod) {
    $strMethodName = $objMethod->getName();
    $objClass = $objMethod->getDeclaringClass();

    if ($frgList = $this->inheritanceListFragmentForMethodNameInClass($strMethodName, $objClass)) {
      return new RBXMLEscapedFragment([
        new RBXMLTag('b', [], [
          RBHyperlink::forClassType($objClass->getName()),
          RBReflectionMethodGetCallType($objMethod),
          $strMethodName
        ]),
        $frgList,
      ]);
    }

    return new RBInactiveText('(no method overwritten or interface implemented)', 'i');
  }

  private function inheritanceListFragmentForMethodNameInClass($strMethodName, ReflectionClass $objClass) {
    $blnMethodInParentClasses = $this->parentClassesHaveMethod($objClass, $strMethodName);
    $blnMethodInImplementedInterfaces = $this->implementedInterfacesInTypeHaveMethod($objClass, $strMethodName);
    $blnMethodInUsedTraits = $this->usedTraitsInClassHaveMethod($objClass, $strMethodName);

    if ($blnMethodInParentClasses || $blnMethodInImplementedInterfaces || $blnMethodInUsedTraits) {
      $objList = new RBXMLTag('ul');

      if ($blnMethodInParentClasses) {
        $objParentClass = $objClass->getParentClass();
        $frgClasses = $this->inheritanceSubtreeFragmentForMethodNameInClass($strMethodName, $objParentClass);
        $objList->appendContentFragment($frgClasses);
      }

      if ($blnMethodInImplementedInterfaces) {
        $lstInterfaces = RBReflectionClassInheritanceGetInterfaces($objClass);
        $this->appendInterfacesToListForMethodName($lstInterfaces, $objList, $strMethodName);
      }

      if ($blnMethodInUsedTraits) {
        $lstTraits = $objClass->getTraits();
        $this->appendTraitsToListForMethodName($lstTraits, $objList, $strMethodName);
      }

      return $objList;
    }

    return null;
  }

  private function inheritanceSubtreeFragmentForMethodNameInClass($strMethodName, ReflectionClass $objClass) {
    $objMethod = null;

    try {
      $objMethod = $objClass->getMethod($strMethodName);
    } catch (ReflectionException $objException) {}

    if ($objMethod && $objMethod->getDeclaringClass() == $objClass) {
      $objFragment = new RBXMLTag('li', [], [
        'overwrites ',
        RBHyperlink::forMethod($objMethod)
      ]);
    } else {
      $objFragment = new RBXMLTag('li', [], [
        'class ',
        RBHyperlink::forClassType($objClass->getName()),
      ]);
    }

    if ($frgList = $this->inheritanceListFragmentForMethodNameInClass($strMethodName, $objClass)) {
      $objFragment->appendContentFragment($frgList);
    }

    return $objFragment;
  }

  private function inheritanceSubtreeFragmentForMethodNameInInterface($strMethodName, ReflectionClass $objInterface) {
    $objMethod = null;

    try {
      $objMethod = $objInterface->getMethod($strMethodName);
    } catch (ReflectionException $objException) {}

    if ($objMethod && $objMethod->getDeclaringClass() == $objInterface) {
      $objFragment = new RBXMLTag('li', [], [
        'implements ',
        RBHyperlink::forMethod($objMethod)
      ]);
    } else {
      $objFragment = new RBXMLTag('li', [], [
        'interface ',
        RBHyperlink::forClassType($objInterface->getName()),
      ]);
    }

    $blnMethodIsImplemented = $this->implementedInterfacesInTypeHaveMethod($objInterface, $strMethodName);
    if ($blnMethodIsImplemented && $lstInterfaces = RBReflectionClassInheritanceGetInterfaces($objInterface)) {
      $objList = $objFragment->appendContentFragment(new RBXMLTag('ul'));
      $this->appendInterfacesToListForMethodName($lstInterfaces, $objList, $strMethodName);
    }

    return $objFragment;
  }

  private function inheritanceSubtreeFragmentForMethodNameInTrait($strMethodName, ReflectionClass $objTrait) {
    $objMethod = null;

    try {
      $objMethod = $objTrait->getMethod($strMethodName);
    } catch (ReflectionException $objException) {}

    if ($objMethod && $objMethod->getDeclaringClass() == $objTrait) {
      $objFragment = new RBXMLTag('li', [], [
        'is ',
        RBHyperlink::forMethod($objMethod)
      ]);
    } else {
      $objFragment = new RBXMLTag('li', [], [
        'trait ',
        RBHyperlink::forClassType($objTrait->getName()),
      ]);
    }

    $blnMethodIsImplemented = $this->usedTraitsInClassHaveMethod($objTrait, $strMethodName);
    if ($blnMethodIsImplemented && $lstTraits = $objTrait->getTraits()) {
      $objList = $objFragment->appendContentFragment(new RBXMLTag('ul'));
      $this->appendTraitsToListForMethodName($lstTraits, $objList, $strMethodName);
    }

    return $objFragment;
  }

  private function appendInterfacesToListForMethodName(array $lstInterfaces, RBXMLTag $objList, $strMethodName) {
    ksort($lstInterfaces, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($lstInterfaces as $objInterface) {
      if (!($frgInterface = $this->inheritanceSubtreeFragmentForMethodNameInInterface($strMethodName, $objInterface))) {
        break;
      }

      $objList->appendContentFragment($frgInterface);
    }
  }

  private function appendTraitsToListForMethodName(array $lstTraits, RBXMLTag $objList, $strMethodName) {
    ksort($lstTraits, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($lstTraits as $objTrait) {
      if (!($frgTrait = $this->inheritanceSubtreeFragmentForMethodNameInTrait($strMethodName, $objTrait))) {
        break;
      }

      $objList->appendContentFragment($frgTrait);
    }
  }

  private function parentClassesHaveMethod(ReflectionClass $objClass, $strMethodName) {
    $blnHaveMethod = false;

    while ($objParentClass = $objClass->getParentClass()) {
      try {
        $objParentClass->getMethod($strMethodName);
        $blnHaveMethod = true;
        break;
      } catch (ReflectionException $objException) {
        $objClass = $objParentClass;
      }
    }

    return $blnHaveMethod;
  }

  private function implementedInterfacesInTypeHaveMethod(ReflectionClass $objClass, $strMethodName) {
    $blnHaveMethod = false;

    foreach (RBReflectionClassInheritanceGetInterfaces($objClass) as $objInterface) {
      try {
        $objInterface->getMethod($strMethodName);
        $blnHaveMethod = true;
        break;
      } catch (ReflectionException $objException) {}
    }

    return $blnHaveMethod;
  }

  private function usedTraitsInClassHaveMethod(ReflectionClass $objClass, $strMethodName) {
    $blnHaveMethod = false;

    foreach ($objClass->getTraits() as $objTrait) {
      try {
        $objTrait->getMethod($strMethodName);
        $blnHaveMethod = true;
        break;
      } catch (ReflectionException $objException) {}
    }

    return $blnHaveMethod;
  }
}
