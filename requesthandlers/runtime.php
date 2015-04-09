<?php

final class RBRuntimeRequestHandler extends RBRequestHandler {
  public function breadcrumbs() {
    return ['phpinfo()'];
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    ob_start();
    phpinfo();
    $strSource = ob_get_clean();

    $strSource = html_entity_decode($strSource);
    $strSource = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $strSource);
    $xmlInfoPage = simplexml_load_string($strSource);
    unset($strSource, $xmlSource);

    $strContent = $xmlInfoPage->body->div->asXML();
    unset($xmlInfoPage);

    $lstStyleRules = [
      'div#phpinfo {color: #222; font-family: sans-serif;}',
      'div#phpinfo pre {margin: 0; font-family: monospace;}',
      'div#phpinfo a {color: #222;}',
      'div#phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}',
      'div#phpinfo a:hover {text-decoration: underline;}',
      'div#phpinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}',
      'div#phpinfo .center {text-align: center;}',
      'div#phpinfo .center table {margin: 1em auto; text-align: left;}',
      'div#phpinfo .center th {text-align: center !important;}',
      'div#phpinfo td, div#phpinfo th {border: 1px solid #666; line-height: 1.1; font-size: 85%; vertical-align: baseline; padding: 4px 5px;}',
      'div#phpinfo h1 {font-size: 175%; font-weight: bold; color: #222;}',
      'div#phpinfo h2 {font-size: 140%; font-weight: bold; color: #222;}',
      'div#phpinfo .p {text-align: left;}',
      'div#phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}',
      'div#phpinfo .h {background-color: #99c; font-weight: bold;}',
      'div#phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto;}',
      'div#phpinfo .v i {color: #999;}',
      'div#phpinfo img {float: right; border: 0;}',
      'div#phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}',
      'div#phpinfo p {color: #222;}',
    ];

    $objRootFragment->appendContentFragments([
      new RBXMLTag('style', ['type' => 'text/css'], [implode("\n", $lstStyleRules)]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['id' => 'phpinfo'], [
          new RBXMLInjectFreeformXSSFragment($strContent)
        ]),
      ]),
    ]);
  }
}
