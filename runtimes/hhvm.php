<?php

final class RBHHVMRuntime extends RBRuntime {
  public function name() {
    return 'HipHop Virtual Machine';
  }

  public function shortName() {
    return 'HHVM';
  }

  public function infoMap() {
    return [
      'Version HHVM' => sprintf('%s (%s)', ini_get('hphp.compiler_version'), ini_get('hhvm.build_type')),
      'Hack syntax' => ini_get('hhvm.force_hh') ? 'Forced' : 'Opt-in',
      'XHP' => ini_get('hhvm.enable_xhp') ? 'Enabled' : 'Disabled',
      'Zend compatibility' => ini_get('hhvm.enable_zend_compat') ? 'Enabled' : 'Disabled',
      'Zend sorting' => ini_get('hhvm.enable_zend_sorting') ? 'Enabled' : 'Disabled',
    ];
  }

  public function defaultValueForParameter(ReflectionParameter $objParameter) {
    if (isset($objParameter->info['defaultText'])) {
      $strDefaultText = $objParameter->info['defaultText'];

      if ($strDefaultText === "array (\n)") {
        return '[]';
      }

      return $strDefaultText;
    }

    return null;
  }

  public function extraInfoMapForExtension(ReflectionExtension $objExtension) {
    return [];
  }

  public function longSourceLocationFragmentForReflector(Reflector $objReflector) {
    if (RBReflectorIsInternal($objReflector)) {
      return 'internal';
    }

    return RBReflectorGetSourceLocation($objReflector);
  }

  public function manualURLForIdentifier($strIdentifier) {
    return "http://docs.hhvm.com/$strIdentifier";
  }

  public function nameFragmentForConstantExtensionName($strName) {
    return $strName === 'Core' ? 'internal' : 'userland';
  }

  public function nameFragmentForExtensionName($strName) {
    return $strName;
  }

  public function sourceLocationFragmentForReflector(Reflector $objReflector) {
    if (RBReflectorIsInternal($objReflector)) {
      return 'internal';
    }

    return new RBXMLTag('span', ['title' => RBReflectorGetSourceLocation($objReflector)], ['userland']);
  }

  public function typeHintForParameter(ReflectionParameter $objParameter) {
    if ($strTypeHint = $objParameter->info['type_hint']) {
      return str_replace('HH\\', '', $strTypeHint);
    }

    return null;
  }

  public function typeHintFragmentForParameter(ReflectionParameter $objParameter) {
    if ($strTypeHint = $this->typeHintForParameter($objParameter)) {
      if ($strTypeHint[0] === '?' && $frgLink = RBHyperlink::forClassType(substr($strTypeHint, 1))) {
        return new RBXMLEscapedFragment(['?', $frgLink]);
      }

      return parent::typeHintFragmentForParameter($objParameter);
    }

    return null;
  }
}
