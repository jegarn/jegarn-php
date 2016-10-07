<?php

namespace Jegarn\Client;

class ErrorObject{
    const NETWORK_ERROR = 0;
    const RECV_PACKET_CRASHED = 1;
    const RECV_PACKET_TYPE = 2;
    const AUTH_FAILED = 3;
    const SEND_PACKET_VALID = 4;
    public $code;
    public $message;
    public function __construct($code, $message){
        $this->code = $code;
        $this->message = $message;
    }
}