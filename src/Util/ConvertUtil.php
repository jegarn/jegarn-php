<?php

namespace Jegarn\Util;

abstract class ConvertUtil {
    public static function pack($mixed){
        return msgpack_pack($mixed);
    }
    public static function unpack($string){
        return msgpack_unpack($string);
    }
}