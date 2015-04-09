<?php

final class RBInterfacesRequestHandler extends RBClassRequestHandler {
  protected function masterName() {
    return 'Interfaces';
  }

  protected function masterTemplateClass() {
    return 'RBInterfacesTemplateTable';
  }
}
