<?php

namespace Jegarn\Packet;

class FriendRequestNotification extends FriendNotification{
    const SUB_TYPE = 'friend_request';
    public function __construct(){
        parent::__construct();
    }
}