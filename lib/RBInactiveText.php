<?php

final class RBInactiveText extends RBTemplateFragment {
  public function __construct($frgText, $strTagName = 'span') {
    parent::__construct(new RBXMLTag($strTagName, ['style' => 'color: #bbb;'], [$frgText]));
  }
}
