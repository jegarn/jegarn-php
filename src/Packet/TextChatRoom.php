<?php

namespace Jegarn\Packet;

class TextChatRoom extends ChatRoom
{
    const SUB_TYPE = 'text';

    public function __construct()
    {
        parent::__construct();
    }

    public function getText()
    {
        return isset($this->content['text']) ? $this->content['text'] : null;
    }

    public function setText($value)
    {
        $this->content['text'] = $value;
    }
}