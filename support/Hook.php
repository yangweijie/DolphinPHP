<?php
namespace support;
class Hook
{
    function __construct(){

    }

    function exec($data, $event_name){
        $hook_name = str_replace('hook.', '', $event_name);

    }
}