<?php
/**
 * Created by IntelliJ IDEA.
 * User: lalittanwar
 * Date: 20/09/15
 * Time: 12:43 AM
 */


namespace app\service {

    class DBService
    {
        public static $connected = false;
        public static $map = array();
        public static $defaultDb = null;

        public static function getDB()
        {
            if (self::$defaultDb == null) {
                self::$defaultDb = self::initDB(\Config::getProperty("GLOBAL", "DEFAULT_DB"));
            }
            return self::$defaultDb;
        }

        public static function close($configname = NULL)
        {
            if ($configname == NULL && self::$defaultDb != null) {
                self::$defaultDb->close();
            }
        }

        public static function initDB($configname)
        {
            if (!self::$connected) {
                self::$connected = true;
            }
            self::$map [$configname] = new \app\model\AbstractDb (\Config::getSection($configname));
            return self::$map [$configname];
        }
    }

}
