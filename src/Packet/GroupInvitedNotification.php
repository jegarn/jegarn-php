<?php

namespace Jegarn\Packet;

class GroupInvitedNotification extends GroupNotification{
    const SUB_TYPE = 'group_invited';
    public function __construct(){
        parent::__construct();
    }
}