<?php

namespace Jegarn\Packet;

class FriendRefusedNotification extends FriendNotification{
    const SUB_TYPE = 'friend_refused';
    public function __construct(){
        parent::__construct();
    }
}