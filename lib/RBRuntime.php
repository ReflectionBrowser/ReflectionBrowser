<?php

abstract class RBRuntime {
  abstract public function name();
  abstract public function shortName();
  abstract public function infoMap();
  public function navigationItems() {
    return [];
  }

  abstract public function defaultValueForParameter(ReflectionParameter $objParameter);
  abstract public function extraInfoMapForExtension(ReflectionExtension $objExtension);
  abstract public function longSourceLocationFragmentForReflector(Reflector $objReflector);
  abstract public function manualURLForIdentifier($strIdentifier);
  public function manualURLForReflector(Reflector $objReflector) {
    if (RBReflectorIsInternal($objReflector)) {
      if ($objReflector instanceof ReflectionMethod) {
        $strName = $objReflector->getName();

        if ($strName[0] === '_' && $strName[1] === '_') {
          $strName = substr($strName, 2);
        }

        $strIdentifier = sprintf('%s.%s', $objReflector->getDeclaringClass()->getName(), $strName);
      } else {
        $strIdentifier = RBReflectorGetName($objReflector);
      }

      return $this->manualURLForIdentifier(strtolower($strIdentifier));
    }

    return null;
  }
  abstract public function nameFragmentForConstantExtensionName($strName);
  abstract public function nameFragmentForExtensionName($strName);
  abstract public function sourceLocationFragmentForReflector(Reflector $objReflector);
  abstract public function typeHintForParameter(ReflectionParameter $objParameter);
  public function typeHintFragmentForParameter(ReflectionParameter $objParameter) {
    if ($strTypeHint = $this->typeHintForParameter($objParameter)) {
      if ($frgTypeHint = RBHyperlink::forClassType($strTypeHint)) {
        return $frgTypeHint;
      }

      return $strTypeHint;
    }

    return new RBInactiveText('(no type hint available)', 'i');
  }
}
