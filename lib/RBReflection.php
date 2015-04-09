<?php

function RBReflectorGetExtensionName(Reflector $objReflector) {
  if (!($objReflector instanceof ReflectionFunctionAbstract || $objReflector instanceof ReflectionClass)) {
    throw new InvalidArgumentException(get_class($objReflector) . ' cannot tell an extension name');
  }

  return $objReflector->getExtensionName();
}

function RBReflectorGetName(Reflector $objReflector) {
  return $objReflector->getName();
}

function RBReflectorGetSourceLocation(Reflector $objReflector) {
  if (!($objReflector instanceof ReflectionFunctionAbstract || $objReflector instanceof ReflectionClass)) {
    throw new InvalidArgumentException(get_class($objReflector) . ' has no source information');
  }

  return sprintf('%s(%d-%d)', $objReflector->getFileName(), $objReflector->getStartLine(), $objReflector->getEndLine());
}

function RBReflectorGetVisibilty(Reflector $objReflector) {
  if (!($objReflector instanceof ReflectionMethod || $objReflector instanceof ReflectionProperty)) {
    throw new InvalidArgumentException(get_class($objReflector) . ' has no visibility information');
  }

  if ($objReflector->isPublic()) {
    return 'public';
  }

  if ($objReflector->isProtected()) {
    return 'protected';
  }

  return 'private';
}

function RBReflectorIsInternal(Reflector $objReflector) {
  if (!($objReflector instanceof ReflectionFunctionAbstract || $objReflector instanceof ReflectionClass)) {
    return true;
  }

  return $objReflector->isInternal();
}

function RBReflectorIsReflectionBrowserSymbol(Reflector $objReflector) {
  if ($objReflector instanceof ReflectionFunctionAbstract || $objReflector instanceof ReflectionClass) {
    if ($strFileName = $objReflector->getFileName()) {
      return strpos($strFileName, realpath(dirname($_SERVER['SCRIPT_FILENAME']))) === 0;
    }
  }

  return false;
}

function RBReflectionClassExtensibilityGetFragment(ReflectionClass $objClass) {
  if ($objClass->isAbstract()) {
    return 'abstract';
  }

  if ($objClass->isFinal()) {
    return 'final';
  }

  return 'extensible';
}

function RBReflectionClassInheritanceGetInterfaces(ReflectionClass $objClass) {
  if ($lstInterfaces = $objClass->getInterfaces()) {
    $lstInterfacesFixed = $lstInterfaces;

    foreach ($lstInterfaces as $objInterface) {
      foreach ($objInterface->getInterfaces() as $objParentInterface) {
        unset($lstInterfacesFixed[$objParentInterface->getName()]);
      }
    }

    if ($objParentClass = $objClass->getParentClass()) {
      foreach ($objParentClass->getInterfaces() as $objInterface) {
        unset($lstInterfacesFixed[$objInterface->getName()]);
      }
    }

    return $lstInterfacesFixed;
  }

  return [];
}

function RBReflectionClassGetType(ReflectionClass $objClass) {
  if ($objClass->isInterface()) {
    return 'interface';
  }

  if ($objClass->isTrait()) {
    return 'trait';
  }

  return 'class';
}

function RBReflectionMethodFromStaticName($strName) {
  list($strClassName, $strMethodName) = explode('::', $strName);
  return new ReflectionMethod($strClassName, $strMethodName);
}

function RBReflectionMethodGetCallType(ReflectionMethod $objMethod) {
  if ($objMethod->isStatic()) {
    return '::';
  }

  return '->';
}

function RBReflectionMethodGetName(ReflectionMethod $objMethod) {
  $strName = $objMethod->getDeclaringClass()->getName();
  $strName .= RBReflectionMethodGetCallType($objMethod);
  $strName .= $objMethod->getName();
  return $strName;
}
