<?php

namespace Jegarn\Packet;

class GroupRefusedNotification extends GroupNotification{
    const SUB_TYPE = 'group_refused';
    public function __construct(){
        parent::__construct();
    }
}