<?php

$str = <<<EOF
class BaseManager {
        private static \$instance;
        private function __construct(){}
        private function __clone() {}
        /**
         * @param null \$class
         * @return static
         * @throws \Exception
         */
        public static function getInstance(\$class = null){
            if(\$class === null){
                throw new Exception('class name can\'t be null');
            }else{
                if(!isset(self::\$instance[\$class])){
                    self::\$instance[\$class] = new \$class;
                }
            }
            return self::\$instance[\$class];
        }
    }
    class UserManager extends BaseManager{
        public static function getInstance(\$class = __CLASS__){
            return parent::getInstance(\$class);
        }
        public function addUser(\$uid, \$account, \$password){
            return Cache::getInstance()->set(\$this->getCacheKey(\$account), ConvertUtil::pack(['uid' => \$uid, 'account' => \$account, 'password' => \$this->encryptPassword(\$password)]));
        }
        public function encryptPassword(\$password){
            return hash('sha256', \$password);
        }
        public function authPassword(\$input, \$cryptPassword){
            return \$input && \$this->encryptPassword(\$input) == \$cryptPassword;
        }
        public function getUser(\$account){
            if(\$str = Cache::getInstance()->get(\$this->getCacheKey(\$account))){
                return ConvertUtil::unpack(\$str);
            }
            return null;
        }
        public function removeUser(\$account){
            Cache::getInstance()->del(\$this->getCacheKey(\$account));
        }
        public function isUserOnline(\$uid){
            return Cache::getInstance()->exists('S_'.\$uid);
        }
        public function getAllOnlineUsers(){
            \$cache = Cache::getInstance();
            \$cache->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
            \$it = null;
            \$uidList = [];
            while(\$keys = \$cache->scan(\$it, 'S_*', 1000)){
                foreach(\$keys as \$key){
                    \$uidList[] = substr(\$key,2);
                }
            }
            return \$uidList;
        }
        protected function getCacheKey(\$id){
            return 'U_' . \$id;
        }
    }
    class GroupManager extends BaseManager{
        public static function getInstance(\$class = __CLASS__){
            return parent::getInstance(\$class);
        }
        public function addGroup(\$gid){
            return true;
        }
        public function removeGroup(\$gid){
            return Cache::getInstance()->del(\$this->getCacheKey(\$gid)) > 0;
        }
        public function addGroupUser(\$gid, \$uid){
            return Cache::getInstance()->sAdd(\$this->getCacheKey(\$gid), \$uid);
        }
        public function isGroupUser(\$gid, \$uid){
            return Cache::getInstance()->sIsMember(\$this->getCacheKey(\$gid), \$uid);
        }
        public function addGroupUsers(\$gid, \$uidList){
            array_unshift(\$uidList, \$this->getCacheKey(\$gid));
            return call_user_func_array([Cache::getInstance(), 'sAdd'], \$uidList);
        }
        public function getGroupUsers(\$gid){
            return Cache::getInstance()->sMembers(\$this->getCacheKey(\$gid));
        }
        public function removeGroupUser(\$gid, \$uid){
            return Cache::getInstance()->sRem(\$this->getCacheKey(\$gid), \$uid);
        }
        public function removeGroupUsers(\$gid, \$uidList){
            array_unshift(\$uidList, \$this->getCacheKey(\$gid));
            call_user_func_array([Cache::getInstance(), 'sRem'], \$uidList);
        }
        protected function getCacheKey(\$id){
            return 'G_' . \$id;
        }
    }
    class ChatRoomManager extends BaseManager{
        public static function getInstance(\$class = __CLASS__){
            return parent::getInstance(\$class);
        }
        public function addChatRoom(\$rid){
            return true;
        }
        public function removeChatRoom(\$rid){
            return Cache::getInstance()->del(\$this->getCacheKey(\$rid)) > 0;
        }
        public function addChatRoomUser(\$rid, \$uid){
            return Cache::getInstance()->sAdd(\$this->getCacheKey(\$rid), \$uid);
        }
        public function isGroupUser(\$rid, \$uid){
            return Cache::getInstance()->sIsMember(\$this->getCacheKey(\$rid), \$uid);
        }
        public function addChatRoomUsers(\$rid, \$uidList){
            array_unshift(\$uidList, \$this->getCacheKey(\$rid));
            return call_user_func_array([Cache::getInstance(), 'sAdd'], \$uidList);
        }
        public function getGroupUsers(\$rid){
            return Cache::getInstance()->sMembers(\$this->getCacheKey(\$rid));
        }
        public function removeChatRoomUser(\$rid, \$uid){
            return Cache::getInstance()->sRem(\$this->getCacheKey(\$rid), \$uid);
        }
        public function removeChatRoomUsers(\$rid, \$uidList){
            array_unshift(\$uidList, \$this->getCacheKey(\$rid));
            call_user_func_array([Cache::getInstance(), 'sRem'], \$uidList);
        }
        protected function getCacheKey(\$id){
            return 'R_' . \$id;
        }
    }
    class SocketManager extends BaseManager {
        protected \$clients;
        public static function getInstance(\$class = __CLASS__){
            return parent::getInstance(\$class);
        }
        public function closeClient(\$id){
            if(isset(\$this->clients[\$id])){
                \$this->clients[\$id] = null;
                unset(\$this->clients[\$id]);
            }
        }

        public function sendClientMessage(\$host, \$port, \$message, \$options){
            \$id = \$this->getClientId(\$host, \$port);
            if(empty(\$message)){
                return false;
            }
            if(!isset(\$this->clients[\$id])){
                \$ssl = isset(\$options['ssl_cert_file']) && \$options['ssl_cert_file'];
                if(\$client = new swoole_client(\$ssl ? SWOOLE_SOCK_TCP | SWOOLE_SSL : SWOOLE_SOCK_TCP)){
                    \$this->clients[\$id] = \$client;
                }else{
                    return false;
                }
                if(!\$client->connect(\$host, \$port, isset(\$options['timeout']) ? \$options['timeout'] : 0.5)){
                    \$this->closeClient(\$id);
                    return false;
                }
            }
            \$client = \$this->clients[\$id];
            \$retryCount = 3;
            send_data:
            --\$retryCount;
            \$messageLen = strlen(\$message);
            \$ret = \$client->send(\$message);
            if(\$ret === \$messageLen){
                return true;
            }else if(\$retryCount <= 0){
                \$this->closeClient(\$id);
                return false;
            }else if(false === \$ret){
                if(!\$client->connect(\$host, \$port, isset(\$options['timeout']) ? \$options['timeout'] : 0.1)){
                    \$this->closeClient(\$id);
                    return false;
                }
                goto send_data;
            }else/*if(\$ret !== \$messageLen)*/{
                \$message = substr(\$message, \$ret);
                goto send_data;
            }
        }

        protected function getClientId(\$host, \$port){
            return \$host . ':' . \$port;
        }
    }
EOF;


preg_match_all('/class (\S+)/', $str, $m);

print_r($m[1]);

foreach($m[1] as $name){
    file_put_contents($name . '.php', <<<EOF
<?php

namespace Jegarn\Packet;


EOF
);
}