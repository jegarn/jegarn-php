<?php

namespace Jegarn\Packet;

class Notification extends HasSubType {
    const TYPE = 'notification';
    public function __construct(){
        parent::__construct();
        $this->setFromSystemUser();
    }
}