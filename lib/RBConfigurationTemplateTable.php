<?php

final class RBConfigurationTemplateTable extends RBExtensionDataTemplateTable {
  use RBDataTemplateTableMapSorter;

  protected function emptyTableText() {
    return 'no configuration options';
  }

  protected function sourceDataForAllExtension() {
    return $this->sourceDataForExtensionList(get_loaded_extensions());
  }

  protected function sourceDataForExtensionName($strExtensionName) {
    return $this->sourceDataForExtensionList([$strExtensionName]);
  }

  private function sourceDataForExtensionList($lstExtensions) {
    $mapConfiguration = [];

    foreach ($lstExtensions as $strExtension) {
      foreach (ini_get_all(strtolower($strExtension)) as $strName => $mapEntry) {
        $mapConfiguration[$strName] = [$strExtension, $mapEntry];
      }
    }

    return $mapConfiguration;
  }

  protected function rowTupleForData($mixKey, $mixValue, RBRuntime $objRuntime) {
    list($strExtension, $mapEntry) = $mixValue;
    $lstColumns = [];

    $frgExtensionName = $objRuntime->nameFragmentForExtensionName($strExtension);
    if (!$this->isSingleExtensionTable() && $frgExtensionName) {
      $lstColumns[] = $frgExtensionName;
    }

    $lstColumns = array_merge($lstColumns, [
      $this->fragmentForConfigurationAccessLevel($mapEntry['access']),
      $this->fragmentForConfigurationEntryMap($mixKey, $mapEntry)
    ]);

    return [
      [$mixKey],
      $lstColumns
    ];
  }

  private function fragmentForConfigurationAccessLevel($intAccessLevel) {
    $mapAccessLevels = ['USER' => false, 'PERDIR' => false, 'SYSTEM' => false];

    if ($intAccessLevel & INI_USER) {
      $mapAccessLevels['USER'] = true;
    }

    if ($intAccessLevel & INI_PERDIR) {
      $mapAccessLevels['PERDIR'] = true;
    }

    if ($intAccessLevel & INI_SYSTEM) {
      $mapAccessLevels['SYSTEM'] = true;
    }

    return $this->monospacedFragmentForFragments([
      $this->fragmentForAccessLevel('USER', $mapAccessLevels['USER']),
      ', ',
      $this->fragmentForAccessLevel('PERDIR', $mapAccessLevels['PERDIR']),
      ', ',
      $this->fragmentForAccessLevel('SYSTEM', $mapAccessLevels['SYSTEM']),
    ]);
  }

  private function fragmentForAccessLevel($strLevel, $blnActive) {
    if ($blnActive) {
      return $strLevel;
    }

    return new RBInactiveText($strLevel);
  }

  private function fragmentForConfigurationEntryMap($strName, array $mapSetting) {
    $mixValue = $mapSetting['global_value'];

    if ($mixValue === null) {
      return new RBInactiveText('not set', 'i');
    }

    if ($mixValue === '') {
      return new RBInactiveText('no value', 'i');
    }

    if (count($mixValue) === 0) {
      return new RBInactiveText('(empty array)', 'i');
    }

    if (strpos($strName, 'highlight.') === 0) {
      $frgValue = $this->monospacedFragmentForString($mapSetting['global_value']);
      $frgValue->setAttributeForKey("color: {$mapSetting['global_value']}", 'style');
      return $frgValue;
    }

    if ($strName === 'disable_classes' || $strName === 'disable_functions' || $strName === 'url_rewriter.tags') {
      $objList = new RBXMLTag('ul');

      foreach (explode("\n", trim(str_replace(',', "\n", $mixValue))) as $strFunction) {
        $objList->appendContentFragment(new RBXMLTag('li', [], [$this->monospacedFragmentForString($strFunction)]));
      }

      return $objList;
    }

    if (is_array($mixValue)) {
      ob_start();
      var_dump($mixValue);
      return $this->monospacedFragmentForString(ob_get_clean());
    }

    return $this->monospacedFragmentForString($mixValue);
  }

  private function monospacedFragmentForFragments(array $lstFragments) {
    return new RBXMLTag('pre', ['class' => 'variables variables-table variables-inline'], $lstFragments);
  }

  private function monospacedFragmentForString($strValue) {
    return $this->monospacedFragmentForFragments([$strValue]);
  }
}
