<?php

abstract class RBTemplateFragment extends RBXMLFragmentStore {
  private $objRootFragment;

  public function __construct(RBXMLFragment $objRoot) {
    $this->objRootFragment = $objRoot;
  }

  public function appendContentString($strContent) {
    return $this->objRootFragment->appendContentString($strContent);
  }

  public function appendContentFragment(RBXMLFragment $objFragment) {
    return $this->objRootFragment->appendContentFragment($objFragment);
  }

  public function appendContentFragments(array $lstFragments) {
    $this->objRootFragment->appendContentFragments($lstFragments);
  }

  public function render() {
    return $this->objRootFragment->render();
  }
}
