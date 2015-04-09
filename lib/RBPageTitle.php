<?php

final class RBPageTitle extends RBTemplateFragment {
  public function __construct($frgTitle, $frgSmallText = null) {
    $lstFragments = [$frgTitle];

    if ($frgSmallText) {
      $lstFragments[] = ' ';
      $lstFragments[] = new RBXMLTag('small', [], [$frgSmallText]);
    }

    parent::__construct(new RBXMLTag('div', ['class' => 'row'], [
      new RBXMLTag('div', ['class' => 'col-lg-12'], [
        new RBXMLTag('h1', ['class' => 'page-header'], $lstFragments)
      ])
    ]));
  }
}
