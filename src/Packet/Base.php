<?php

namespace Jegarn\Packet;

class Base{
    const TYPE = null;
    public $from;
    public $to;
    public $type;
    public $content;
    public function __construct(){
        $this->type = static::TYPE;
    }
    public function isFromSystemUser(){
        return 'system' === $this->from;
    }
    public function setFromSystemUser(){
        $this->from = 'system';
    }
    public function setToSystemUser(){
        $this->to = 'system';
    }
    public function convertToArray(){
        return ['from' => $this->from, 'to' => $this->to, 'type' => $this->type, 'content' => $this->content];
    }
    public static function getPacketFromArray(array $data){
        $packet = new static;
        $packet->from = $data['from'];
        $packet->to = $data['to'];
        $packet->type = $data['type'];
        $packet->content = $data['content'];
        return $packet;
    }
    public function getPacketFromPacket(Base $packet){
        if($packet->type == $this->type){
            $this->from = $packet->from;
            $this->to = $packet->to;
            $this->type = $packet->type;
            $this->content = $packet->content;
            return $this;
        }
        return null;
    }
}