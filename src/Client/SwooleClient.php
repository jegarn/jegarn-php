<?php

namespace Jegarn\Client;

use Jegarn\Packet\Auth;
use Jegarn\Packet\Base;
use Jegarn\Util\ConvertUtil;
use swoole_client;

class SwooleClient extends Client{

    public function connect(){
        parent::connect();
        if(!$this->running){
            $this->running = true;
            $ssl = isset($this->config['ssl_cert_file']) && $this->config['ssl_cert_file'];
            $this->socket = new swoole_client($ssl ? SWOOLE_SOCK_TCP | SWOOLE_SSL : SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
            $this->socket->set($this->config);
            $this->socket->on("error", [$this, 'onError']);
            $this->socket->on("close", [$this, 'onClose']);
            $this->socket->on("connect", [$this, 'onConnect']);
            $this->socket->on("receive", [$this, 'onReceive']);
            $this->socket->connect($this->host, $this->port);
            if($ssl){
                sleep(5); // sleep for connect
            }
        }
    }

    public function onError(swoole_client $cli){
        call_user_func_array($this->errorListener,[new ErrorObject(ErrorObject::NETWORK_ERROR, null), $this]);
    }

    public function onClose(swoole_client $cli){
        call_user_func_array($this->disconnectListener,[$this]);
    }

    public function onConnect(swoole_client $cli){
        $this->auth();
    }

    public function onReceive(swoole_client $cli, $message){
        $packetStr = substr($message,4);
        if(($data = ConvertUtil::unpack($packetStr)) && isset($data[$this->sessionKey], $data['from'], $data['to'], $data['type'], $data['content'])){
            $packet = Base::getPacketFromArray($data);
            if(!$this->authorized){
                if($packet->type == Auth::TYPE){
                    $authPacket = (new Auth())->getPacketFromPacket($packet);
                    switch($authPacket->getStatus()){
                        case Auth::STATUS_NEED_AUTH:
                            $this->auth();
                            break;
                        case Auth::STATUS_AUTH_FAILED:
                            call_user_func_array($this->errorListener,[new ErrorObject(ErrorObject::AUTH_FAILED, $message), $this]);
                            break;
                        case Auth::STATUS_AUTH_SUCCESS:
                            $this->sessionId = $data[$this->sessionKey];
                            $this->authorized = true;
                            $this->uid = $authPacket->getUid();
                            call_user_func_array($this->connectListener,[$this]);
                            break;
                    }
                }else{
                    call_user_func_array($this->errorListener,[new ErrorObject(ErrorObject::RECV_PACKET_TYPE, $message), $this]);
                }
            }else{
                call_user_func_array($this->packetListener,[$packet, $this]);
            }
        }else{
            call_user_func_array($this->errorListener,[new ErrorObject(ErrorObject::RECV_PACKET_CRASHED, $message), $this]);
        }
    }
}