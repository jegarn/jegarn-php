<?php

namespace Jegarn\Packet;

class GroupQuitNotification extends GroupNotification{
    const SUB_TYPE = 'group_quit';
    public function __construct(){
        parent::__construct();
    }
}