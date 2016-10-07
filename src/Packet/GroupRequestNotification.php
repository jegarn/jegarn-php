<?php

namespace Jegarn\Packet;

class GroupRequestNotification extends GroupNotification{
    const SUB_TYPE = 'group_request';
    public function __construct(){
        parent::__construct();
    }
}