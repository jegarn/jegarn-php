<?php

namespace Jegarn\Packet;

class UserNotification extends Notification{
    public function setUserId($uid){
        $this->content['uid'] = $uid;
    }
    public function getUserId(){
        return isset($this->content['uid']) ? $this->content['uid'] : null;
    }
}