<?php

namespace Jegarn\Packet;

class Auth extends Base{
    const TYPE = 'auth';
    const STATUS_NEED_AUTH = 1;
    const STATUS_AUTH_SUCCESS = 2;
    const STATUS_AUTH_FAILED  = 3;
    public function __construct(){
        parent::__construct();
        $this->from = 0;
        $this->setToSystemUser();
    }
    public function getUid(){
        return isset($this->content['uid']) ? $this->content['uid'] : null;
    }
    public function setUid($value){
        $this->content['uid'] = intval($value);
    }
    public function getAccount(){
        return isset($this->content['account']) ? $this->content['account'] : null;
    }
    public function setAccount($value){
        $this->content['account'] = $value;
    }
    public function getPassword(){
        return isset($this->content['password']) ? $this->content['password'] : null;
    }
    public function setPassword($value){
        $this->content['password'] = $value;
    }
    public function getStatus(){
        return isset($this->content['status']) ? $this->content['status'] : null;
    }
    public function setStatus($value){
        $this->content['status'] = $value;
    }
}