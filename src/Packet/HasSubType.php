<?php

namespace Jegarn\Packet;

abstract class HasSubType extends Base{
    const TYPE = '';
    const SUB_TYPE = null;
    public function __construct(){
        parent::__construct();
        $this->content['type'] = static::SUB_TYPE;
    }
}