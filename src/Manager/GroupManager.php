<?php

namespace Jegarn\Manager;

use Jegarn\Cache\Cache;

class GroupManager extends BaseManager{
    public static function getInstance($class = __CLASS__){
        return parent::getInstance($class);
    }
    public function addGroup($gid){
        return true;
    }
    public function removeGroup($gid){
        return Cache::getInstance()->del($this->getCacheKey($gid)) > 0;
    }
    public function addGroupUser($gid, $uid){
        return Cache::getInstance()->sAdd($this->getCacheKey($gid), $uid);
    }
    public function isGroupUser($gid, $uid){
        return Cache::getInstance()->sIsMember($this->getCacheKey($gid), $uid);
    }
    public function addGroupUsers($gid, $uidList){
        array_unshift($uidList, $this->getCacheKey($gid));
        return call_user_func_array([Cache::getInstance(), 'sAdd'], $uidList);
    }
    public function getGroupUsers($gid){
        return Cache::getInstance()->sMembers($this->getCacheKey($gid));
    }
    public function removeGroupUser($gid, $uid){
        return Cache::getInstance()->sRem($this->getCacheKey($gid), $uid);
    }
    public function removeGroupUsers($gid, $uidList){
        array_unshift($uidList, $this->getCacheKey($gid));
        call_user_func_array([Cache::getInstance(), 'sRem'], $uidList);
    }
    protected function getCacheKey($id){
        return 'G_' . $id;
    }
}