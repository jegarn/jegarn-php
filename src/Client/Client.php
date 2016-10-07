<?php

namespace Jegarn\Client;

use Exception;
use Jegarn\Packet\Auth;
use Jegarn\Packet\Base;
use Jegarn\Util\ConvertUtil;

abstract class Client{
    protected $config;
    protected $uid;
    protected $account;
    protected $password;
    protected $host;
    protected $port;
    protected $socket;
    protected $running;
    protected $sessionKey = 'session_id';
    protected $sessionId;
    protected $authorized;
    protected $reconnectInterval;
    protected $packetListener;
    protected $sendListener;
    protected $errorListener;
    protected $connectListener;
    protected $disconnectListener;

    public function setConfig($config){
        $this->config = $config;
    }

    public function connect(){
        if(!$this->host || !$this->port){
            throw new Exception("host or port not config");
        }
        if(!$this->config){
            throw new Exception("client not config");
        }
        if(!$this->packetListener){
            throw new Exception("packetListener not config");
        }
        if(!$this->sendListener){
            throw new Exception("sendListener not config");
        }
        if(!$this->errorListener){
            throw new Exception("errorListener not config");
        }
        if(!$this->connectListener){
            throw new Exception("connectListener not config");
        }
        if(!$this->disconnectListener){
            throw new Exception("disconnectListener not config");
        }
    }

    protected function send($content){
        return $this->socket->send($content);
    }

    public function sendPacket(Base $packet){
        if($this->running){
            if(false !== call_user_func_array($this->sendListener,[$packet, $this])){
                $data = $packet->convertToArray();
                $data[$this->sessionKey] = $this->sessionId;
                $packetStr = ConvertUtil::pack($data);
                return $this->send(pack('N', strlen($packetStr)). $packetStr);
            }
        }
        return false;
    }

    public function auth(){
        if(!$this->account || !$this->password){
            throw new Exception('account or password not config');
        }
        $authPacket = new Auth();
        $authPacket->setAccount($this->account);
        $authPacket->setPassword($this->password);
        $this->sendPacket($authPacket);
    }

    public function isAuthorized(){
        return $this->authorized;
    }

    public function close(){
        $this->running = false;
        $this->socket = null;
    }

    public function reconnect(){
        $this->close();
        $this->connect();
    }

    public function __construct($host, $port, $reconnectInterval){
        $this->host = $host;
        $this->port = $port;
        $this->reconnectInterval = $reconnectInterval;
    }

    public function setUser($account, $password){
        $this->account = $account;
        $this->password = $password;
    }

    public function setPacketListener($packetListener){
        $this->packetListener = $packetListener;
    }

    public function setSendListener($sendListener){
        $this->sendListener = $sendListener;
    }

    public function setErrorListener($errorListener){
        $this->errorListener = $errorListener;
    }

    public function setConnectListener($connectListener){
        $this->connectListener = $connectListener;
    }

    public function setDisconnectListener($disconnectListener){
        $this->disconnectListener = $disconnectListener;
    }
}