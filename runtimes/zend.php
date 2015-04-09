<?php

final class RBZendRuntime extends RBRuntime {
  public function name() {
    return 'Zend Engine';
  }

  public function shortName() {
    return 'Zend';
  }

  public function navigationItems() {
    return [
      new RBReflectOnNavigationItem('runtime', 'phpinfo()', 'info-sign'),
    ];
  }

  public function infoMap() {
    $strMultibyteSupport = $this->phpinfoValueForKey('Zend Multibyte Support');

    return [
      'Debug build' => ZEND_DEBUG_BUILD ? 'Yes' : 'No',
      'Thread safe' => ZEND_THREAD_SAFE ? 'Yes' : 'No',
      'Multibyte support' => $strMultibyteSupport === null ? 'Not available' : ucfirst($strMultibyteSupport),
    ];
  }

  public function defaultValueForParameter(ReflectionParameter $objParameter) {
    if ($objParameter->isDefaultValueAvailable()) {
      if ($objParameter->isDefaultValueConstant()) {
        return $objParameter->getDefaultValueConstantName();
      } else {
        return $objParameter->getDefaultValue();
      }
    } else {
      return false;
    }

    if ($objParameter->allowsNull() && $objParameter->isOptional()) {
      return 'null';
    }

    return null;
  }

  public function extraInfoMapForExtension(ReflectionExtension $objExtension) {
    $blnZendExtension = false;
    try {
      $objZendExtension = new ReflectionZendExtension($objExtension->getName());
      $blnZendExtension = true;
      unset($objZendExtension);
    } catch (Exception $objException) {}

    return [
      'Type' => $blnZendExtension ? 'Zend' : 'PHP',
      'Availability' => $objExtension->isPersistent() ? 'Persistent' : 'Temporary',
    ];
  }

  public function longSourceLocationFragmentForReflector(Reflector $objReflector) {
    if (RBReflectorIsInternal($objReflector)) {
      return $this->nameFragmentForExtensionName(RBReflectorGetExtensionName($objReflector));
    }

    return RBReflectorGetSourceLocation($objReflector);
  }

  public function manualURLForIdentifier($strIdentifier) {
    return "http://php.net/$strIdentifier";
  }

  public function nameFragmentForConstantExtensionName($strName) {
    return $this->nameFragmentForExtensionName($strName);
  }

  public function nameFragmentForExtensionName($strName) {
    return new RBHyperlink($strName, 'index.php?reflectOn=extensions&name=' . urlencode($strName));
  }

  public function sourceLocationFragmentForReflector(Reflector $objReflector) {
    if (RBReflectorIsInternal($objReflector)) {
      return $this->nameFragmentForExtensionName(RBReflectorGetExtensionName($objReflector));
    }

    return new RBXMLTag('span', ['title' => RBReflectorGetSourceLocation($objReflector)], ['userland']);
  }

  public function typeHintForParameter(ReflectionParameter $objParameter) {
    if ($objParameter->isArray()) {
      return 'array';
    } else if ($objParameter->isCallable()) {
      return 'callable';
    } else if ($objClass = $objParameter->getClass()) {
      return $objClass->getName();
    }

    return null;
  }

  private function phpinfoValueForKey($strKey) {
    static $strSource = null;

    if ($strSource === null) {
      ob_start();
      phpinfo(INFO_GENERAL);
      $strSource = ob_get_clean();
    }

    $lstMatches;
    if (preg_match("/<td.*>$strKey.*<\/td><td.*>(.*)<\/td>/", $strSource, $lstMatches)) {
      return trim($lstMatches[1]);
    }

    return null;
  }
}
