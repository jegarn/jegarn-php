<?php

namespace Jegarn\Packet;

abstract class GroupBase extends HasSubType{
    public function __construct(){
        parent::__construct();
    }
    public function isSendToAll(){
        return 'all' == $this->to;
    }
    public function setSendToAll(){
        $this->to = 'all';
    }
    public function getGroupId(){
        return isset($this->content['group_id']) ? $this->content['group_id'] : null;
    }
    public function setGroupId($groupId){
        $this->content['group_id'] = $groupId;
    }
}