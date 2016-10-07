<?php

namespace Jegarn\Manager;

use Jegarn\Cache\Cache;

class ChatRoomManager extends BaseManager{
    public static function getInstance($class = __CLASS__){
        return parent::getInstance($class);
    }
    public function addChatRoom($rid){
        return true;
    }
    public function removeChatRoom($rid){
        return Cache::getInstance()->del($this->getCacheKey($rid)) > 0;
    }
    public function addChatRoomUser($rid, $uid){
        return Cache::getInstance()->sAdd($this->getCacheKey($rid), $uid);
    }
    public function isGroupUser($rid, $uid){
        return Cache::getInstance()->sIsMember($this->getCacheKey($rid), $uid);
    }
    public function addChatRoomUsers($rid, $uidList){
        array_unshift($uidList, $this->getCacheKey($rid));
        return call_user_func_array([Cache::getInstance(), 'sAdd'], $uidList);
    }
    public function getGroupUsers($rid){
        return Cache::getInstance()->sMembers($this->getCacheKey($rid));
    }
    public function removeChatRoomUser($rid, $uid){
        return Cache::getInstance()->sRem($this->getCacheKey($rid), $uid);
    }
    public function removeChatRoomUsers($rid, $uidList){
        array_unshift($uidList, $this->getCacheKey($rid));
        call_user_func_array([Cache::getInstance(), 'sRem'], $uidList);
    }
    protected function getCacheKey($id){
        return 'R_' . $id;
    }
}

