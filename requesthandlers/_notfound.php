<?php

final class RBNotFoundHandler extends RBRequestHandler {
  public function breadcrumbs() {
    return ['Page not found'];
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $this->setStatusCode(404);

    $objImage = RBXMLTag::emptyTag('img', ['src' => 'template/images/notfound.png', 'style' => 'margin: 50px']);
    $objRootFragment->appendContentFragment($objImage);

    $this->application()->addAttribution(new RBXMLEscapedFragment([
      'Image by ', new RBHyperlink('Magnt', 'http://magnt.com/', true),
    ]));
  }
}
