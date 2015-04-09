<?php

final class RBTemplate extends RBXMLFragmentStore {
  private $objApplication;

  public function __construct(RBApplication $objApplication) {
    parent::__construct([]);
    $this->objApplication = $objApplication;
  }

  public function render() {
    ob_start();
    include(__DIR__ . '/../template/page.php');
    return ob_get_clean();
  }

  public function hasPageContent() {
    return count($this->lstFragments) > 0;
  }

  private function scriptURI() {
    return $this->objApplication->URI();
  }

  private function baseURL() {
    return dirname($this->scriptURI()) . '/';
  }

  private function runtimeName() {
    return $this->objApplication->environment()->runtime()->shortName();
  }

  private function navigationItems() {
    $strURI = $this->scriptURI();

    $lstItems = array_map(function(RBNavigationItem $objNavItem) use ($strURI) {
      if ($objNavItem instanceof RBDividerNavigationItem) {
        return new RBXMLTag('li', ['role' => 'presentation', 'class' => 'divider']);
      }

      $strLinkURI = $strURI;
      $lstAttributes = [];

      if ($objNavItem instanceof RBReflectOnNavigationItem) {
        $strLinkURI .= "?reflectOn=" . $objNavItem->reflectOn();
      }

      if ($this->objApplication->isNavigationItemActive($objNavItem)) {
        $lstAttributes['class'] = 'active';
      }

      $frgLinkText = new RBXMLEscapedFragment([
        new RBXMLTag('span', ['class' => 'glyphicon glyphicon-' . $objNavItem->iconName()]),
        ' ' . $objNavItem->description(),
      ]);

      return new RBXMLTag('li', $lstAttributes, [new RBHyperlink($frgLinkText->render(), $strLinkURI)]);
    }, $this->objApplication->navigationItems());

    $objTag = new RBXMLTag('ul', ['class' => 'nav menu'], $lstItems);
    return $objTag->render();
  }

  private function attributions() {
    $lstAttributions = $this->objApplication->attributions();

    $lstAttributions[] = new RBXMLEscapedFragment([
      'Template by ',
      new RBHyperlink('Medialoot', 'http://www.medialoot.com/item/lumino-admin-bootstrap-template/', true),
    ]);

    $objAttributionsTag = new RBXMLTag('div', ['class' => 'attribution'], [
      new RBXMLInjectFreeformXSSFragment(implode('<br/>', array_map(function(RBXMLFragment $objAttribution) {
        return $objAttribution->render();
      }, $lstAttributions))),
    ]);

    return $objAttributionsTag->render();
  }

  private function breadcrumbItems() {
    $frgLinkText = new RBXMLTag('span', ['class' => 'glyphicon glyphicon-home']);
    $lstItems = [new RBXMLTag('li', [], [new RBHyperlink($frgLinkText->render(), $this->scriptURI())])];

    $strBreadcrumbArray = $this->objApplication->breadcrumbs();
    $intCount = count($strBreadcrumbArray);
    $intLastIndex = $intCount - 1;

    for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
      $lstAttributes = [];

      if ($intIndex === $intLastIndex) {
        $lstAttributes['class'] = 'active';
      }

      $lstItems[] = new RBXMLTag('li', $lstAttributes, [$strBreadcrumbArray[$intIndex]]);
    }

    $objList = new RBXMLTag('ol', ['class' => 'breadcrumb'], $lstItems);
    return $objList->render();
  }

  private function pageContent() {
    return $this->renderFragments();
  }
}
