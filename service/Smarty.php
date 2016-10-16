<?php

namespace app\service {

    class Smarty
    {

        public static $PLUGINS_DIR = array();

        public static function rx_setup()
        {
        }

        public static function addPluginsDir($dir)
        {
            $t = debug_backtrace();
            $path = resolve_path($t[0]['file'] . "/../" . $dir);
            self::$PLUGINS_DIR[] = $path;
        }

    }

    R::rx_setup();
}


