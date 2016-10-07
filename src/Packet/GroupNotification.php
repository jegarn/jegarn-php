<?php

namespace Jegarn\Packet;

class GroupNotification extends UserNotification{
    public function getGroupId(){
        return isset($this->content['group_id']) ? $this->content['group_id'] : null;
    }
    public function setGroupId($groupId){
        $this->content['group_id'] = $groupId;
    }
    public function getGroupName(){
        return isset($this->content['group_name']) ? $this->content['group_name'] : null;
    }
    public function setGroupName($name){
        $this->content['group_name'] = $name;
    }
}