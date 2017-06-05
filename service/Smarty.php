<?php

namespace app\service {

    include_once(RUDRA . "/smarty/Smarty.class.php");

    class Smarty
    {

        public static $PLUGINS_DIR = array();
        public static $VIEW_PATH = VIEW_PATH;

        public static function rx_setup()
        {
            self::addPluginsDir("../plugins");
        }

        public static function getInstance($newInstance = null)
        {
            static $instance = null;
            if (isset($newInstance))
                $instance = $newInstance;
            if ($instance == null)
                $instance = new \Smarty();
            return $instance;
        }

        public static function addPluginsDir($dir)
        {
            $t = debug_backtrace();
            $path = str_replace("\\", "/", $t[0]['file']);
            $path = resolve_path($path . "/../" . $dir);
            self::$PLUGINS_DIR[] = $path;
            return $path;
        }

        public static function setTemplateDir($dir)
        {
            $t = debug_backtrace();
            $path = str_replace("\\", "/", $t[0]['file']);
            $path = resolve_path($path . "/../" . $dir);
            self::$VIEW_PATH = $path;
            return $path;
        }
    }

    Smarty::rx_setup();
}


