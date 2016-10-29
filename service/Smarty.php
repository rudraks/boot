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
            $path = str_replace("\\","/",$t[0]['file']);
            $path = resolve_path($path. "/../" . $dir);
            self::$PLUGINS_DIR[] = $path;
            return $path;
        }

    }

    R::rx_setup();
}


