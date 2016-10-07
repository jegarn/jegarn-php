<?php

namespace Jegarn\Server;

use Exception;
use Jegarn\Cache\Cache;
use Jegarn\Manager\SocketManager;
use Jegarn\Packet\Base;
use Jegarn\Util\ConvertUtil;

class Server{
    protected $config;
    protected $localAddress;
    protected $localPort;
    protected $remoteAddress;
    protected $remotePort;
    protected $serverId;
    protected $registerKey = 'L_server';
    protected $serverKey   = 'server_info';
    private static $_instance;
    private function __construct(){}
    private function __clone(){}
    public static function getInstance(){
        return self::$_instance ? self::$_instance : (self::$_instance = new static);
    }
    public function initConfig($config){
        if(isset($config['localAddress'], $config['localPort'], $config['remoteAddress'], $config['remotePort'], $config['serverId'])){
            $this->localAddress = $config['localAddress'];
            $this->localPort = $config['localPort'];
            $this->remoteAddress = $config['remoteAddress'];
            $this->remotePort = $config['remotePort'];
            $this->serverId = $config['serverId'];
            $this->config = $config;
            return $this;
        }else{
            throw new Exception('server config is not completed');
        }
    }
    public function register(){
        Cache::getInstance()->hSet($this->registerKey, $this->serverId, $this->localAddress . ':' . $this->localPort);
    }
    public function sendPacket(Base $packet){
        $data = $packet->convertToArray();
        $data[$this->serverKey] = $this->serverId;
        $packetStr = ConvertUtil::pack($data);
        SocketManager::getInstance()->sendClientMessage($this->remoteAddress, $this->remotePort, pack('N',strlen($packetStr)).$packetStr, $this->config);
    }
}