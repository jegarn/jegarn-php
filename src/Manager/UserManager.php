<?php

namespace Jegarn\Manager;

use Jegarn\Cache\Cache;
use Jegarn\Util\ConvertUtil;
use Redis;

class UserManager extends BaseManager{
    public static function getInstance($class = __CLASS__){
        return parent::getInstance($class);
    }
    public function addUser($uid, $account, $password){
        return Cache::getInstance()->set($this->getCacheKey($account), ConvertUtil::pack(['uid' => $uid, 'account' => $account, 'password' => $this->encryptPassword($password)]));
    }
    public function encryptPassword($password){
        return hash('sha256', $password);
    }
    public function authPassword($input, $cryptPassword){
        return $input && $this->encryptPassword($input) == $cryptPassword;
    }
    public function getUser($account){
        if($str = Cache::getInstance()->get($this->getCacheKey($account))){
            return ConvertUtil::unpack($str);
        }
        return null;
    }
    public function removeUser($account){
        Cache::getInstance()->del($this->getCacheKey($account));
    }
    public function isUserOnline($uid){
        return Cache::getInstance()->exists('S_'.$uid);
    }
    public function getAllOnlineUsers(){
        $cache = Cache::getInstance();
        $cache->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $it = null;
        $uidList = [];
        while($keys = $cache->scan($it, 'S_*', 1000)){
            foreach($keys as $key){
                $uidList[] = substr($key,2);
            }
        }
        return $uidList;
    }
    protected function getCacheKey($id){
        return 'U_' . $id;
    }
}