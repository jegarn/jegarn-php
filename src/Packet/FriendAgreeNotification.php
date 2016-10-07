<?php

namespace Jegarn\Packet;

class FriendAgreeNotification extends FriendNotification{
    const SUB_TYPE = 'friend_agree';
    public function __construct(){
        parent::__construct();
    }
}