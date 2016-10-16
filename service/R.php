<?php

namespace app\service {

    class R extends \RedBeanPHP\R
    {

        public static $RX_CONNECTED = false;

        public static function rx_setup()
        {
            $config = \Config::getSection("REDBEAN_CONFIG");
            if (!self::$RX_CONNECTED) {
                self::setup('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password']);
                self::$RX_CONNECTED = true;
            }
        }

    }
    R::rx_setup();
}


