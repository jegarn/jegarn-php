<?php

namespace Jegarn\Packet;

class GroupDisbandNotification extends GroupNotification{
    const SUB_TYPE = 'group_disband';
    public function __construct(){
        parent::__construct();
    }
}