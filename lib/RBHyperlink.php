<?php

final class RBHyperlink extends RBTemplateFragment {
  public function __construct($strText, $strURL, $blnNewWindow = false) {
    $strExtraAttributes = '';

    if ($blnNewWindow) {
      $strExtraAttributes = ' target="_blank"';
    }

    parent::__construct(new RBXMLInjectFreeformXSSFragment("<a href=\"$strURL\"$strExtraAttributes>$strText</a>"));
  }

  public static function internalReflectOn($reflectOn, $frgText, $strName = null) {
    $strURL = "index.php?reflectOn=$reflectOn";

    if ($strName !== null) {
      $strURL .= '&name=' . urlencode($strName);
    }

    return new self(RBXMLFragmentRender($frgText), $strURL);
  }

  public static function forMethod(ReflectionMethod $objMethod) {
    $strName = RBReflectionMethodGetName($objMethod);
    return self::internalReflectOn('functions', $strName, $strName);
  }

  public static function forClassType($strName) {
    if (class_exists($strName, false)) {
      return self::internalReflectOn('classes', $strName, $strName);
    }

    if (interface_exists($strName, false)) {
      return self::internalReflectOn('interfaces', $strName, $strName);
    }

    if (trait_exists($strName, false)) {
      return self::internalReflectOn('traits', $strName, $strName);
    }

    return null;
  }
}
