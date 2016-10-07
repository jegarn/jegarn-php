<?php

namespace Jegarn\Packet;

class GroupAgreeNotification extends GroupNotification{
    const SUB_TYPE = 'group_agree';
    public function __construct(){
        parent::__construct();
    }
}