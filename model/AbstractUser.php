<?php

/*
 * To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace app\model {

    /**
     * Description of User
     *
     * @author Lalit Tanwar
     */
    abstract class AbstractUser
    {

        public static $usercache;
        public $valid = FALSE;
        public $uid = -1;
        public $uname = "guest";
        public $password = "";
        public $role = "GUEST";
        private $info;

        public function  __construct()
        {
            if (self::$usercache == NULL) self::$usercache = new RxCache('user');
            $this->info = array();
        }

        public function set($key, $value)
        {
            $this->info[$key] = $value;
        }

        public function get($key)
        {
            return isset($this->info[$key]) ? $this->info[$key] : null;
        }

        public function getData()
        {
            return $this->info;
        }

        public function validate()
        {
            if (!empty($_COOKIE["rx_useruid"]) || !empty($_SESSION["rx_useruid"])) {
                if (isset($_SESSION['uid']) && trim($_SESSION['uid'])) {
                    $info = self::$usercache->get($_SESSION['uid']);
                    if ($info) {
                        $this->valid = TRUE;
                        $this->uid = $_SESSION['uid'];
                        $this->role = $_SESSION['role'];
                        $this->info = $info;
                        return TRUE;
                    }
                }
            }
            return FALSE;
        }

        public function setValid()
        {
            $this->valid = TRUE;
            session_regenerate_id();
            $_SESSION['uid'] = $this->uid;
            $_SESSION['role'] = $this->role;
            $_SESSION['uname'] = $this->uname;
            $this->info['uid'] = $this->uid;
            $this->info['uname'] = $this->uname;


            $encryptedpassword = md5($this->password);
            $rx_salt = md5($this->uname + $encryptedpassword);

            if (isset($_POST['cookiecheck'])) {
                setcookie("rx_useruid", $this->uid, time() + 60 * 60 * 24 * 30, "/");
                setcookie("rx_salt", $rx_salt, time() + 60 * 60 * 24 * 30, "/");
            }
            if (!isset($_SESSION)) session_start();
            $_SESSION["rx_useruid"] = $this->uid;
            $_SESSION["rx_salt"] = $rx_salt;

            $this->save();
            session_write_close();
        }

        public function setInValid()
        {
            $this->valid = FALSE;
            self::$usercache->get($this->uid);
            session_destroy();
        }


        public function basicAuth()
        {
            global $_SERVER;
            global $_SESSION;
            // the valid_user checks the user/password (very primitive test in this example)
            $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
            $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

            if (!$this->auth($username, $password)) {
                session_destroy();
                header("WWW-Authenticate: Basic realm=\"My Rudrax\"");
                header("HTTP/1.0 401 Unauthorized");
                exit();
            }
            header('X-auth-event : true');
            $this->uname = $_SERVER['PHP_AUTH_USER'];
            $this->password = $_SERVER['PHP_AUTH_PW'];
            // OK, the user is authenticated
            $_SESSION['PHP_AUTH_USER'] = $_SERVER['PHP_AUTH_USER'];
        }

        public function basicUnAuth()
        {

            global $_SESSION;
            global $HTTP_SERVER_VARS;
            global $PHP_SELF;
            header('X-auth-event : true');
            if (isset($_SESSION['reauth-in-progress'])) {
                session_destroy();
                //header("Location: http://" . $HTTP_SERVER_VARS['HTTP_HOST'] . $PHP_SELF);
            } else {
                // We mark the session as requiring a re-auth
                $_SESSION['reauth-in-progress'] = 1;
                // This forces the authentication cache clearing
                header("WWW-Authenticate: Basic realm=\"My Rudrax\"");
                header('HTTP/1.1 401 Unauthorized');
                die('Admin access turned off');
                // In case of the user clicks "cancel" in the dialog box
                print '<a href="http://' . $HTTP_SERVER_VARS['HTTP_HOST'] . $PHP_SELF . '">click me</a>';
                exit();
            }

        }

        public abstract function auth($username, $passowrd);

        public function unauth()
        {
            global $HTTP_SERVER_VARS;
            global $PHP_SELF;
            header('X-auth-event : true');
            if (isset($_SESSION['reauth-in-progress'])) {
                session_destroy();
                //header("Location: http://" . $HTTP_SERVER_VARS['HTTP_HOST'] . $PHP_SELF);
            } else
                self::basicUnAuth();
        }

        public function isValid()
        {
            return $this->valid;
        }

        public function save()
        {
            self::$usercache->set($this->uid, $this->info);
        }

    }

    class DefaultUser extends AbstractUser
    {

        public function auth($username, $passowrd)
        {
            if (strcmp($username, "admin") == 0) {
                $this->setValid();
            }
        }

        public function unauth()
        {
            //DO SOME THING
            $this->setInValid();
        }
    }

}


