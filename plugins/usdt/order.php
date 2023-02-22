<?php
define('CURR_PATH', dirname(__DIR__));
require CURR_PATH . '/usdt/usdt_plugin.php';

if (function_exists("set_time_limit")) {
    @set_time_limit(0);
}

usdt_plugin::submit();
?>