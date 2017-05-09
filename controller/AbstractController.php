<?php

namespace app\controller {

    use \app\model\RxCache;
    use \app\model\AbstractUser;
    use \RudraX\Utils\Webapp;
    use \RudraX\Utils\FileUtil;

    abstract class AbstractController
    {
        public static $HEADER_GLUE = ";-$-;";
        public $user;
        private $responseCache;
        private $cacheDuration = 30000;

        public function loadSession()
        {
            $UserClass = \ClassUtil::getSessionUserClass();
            $this->user = new $UserClass ();
        }

        public function setUser(AbstractUser $user)
        {
            $this->user = $user;
        }

        public function getUser()
        {
            return $this->user;
        }

        public function _interpret_($info, $params)
        {
            $nocache = (isset($_REQUEST["_AUTH_"]) || isset($_REQUEST["_NOCACHE_"]));
            $cache = $info ["cache"] && !($nocache);
            $cache_file = ($info ["cache"] === "file") && !empty(Webapp::$REQUEST_FILE_EXT);
            $perform = true;
            $md5key = null;

            $validate = $this->user->validate();

            if ($info["auth"] && !$validate) {
                $this->user->basicAuth();
                $validate = $this->user->validate(); //Validate again 
            }

            if ($info["roles"] !== FALSE) {
                if (!in_array($this->user->role, $info["roles"])) {
                    print_r($info["roles"]);
                    echo($this->user->role);
                    header("HTTP/1.1 403 Unauthorized");
                    exit();
                }
            }
 
            $cache = ($cache || (isset($info ["guestcache"]) && $info ["guestcache"] && !$validate)) && !$nocache;

            header("Pragma:");
            header("X-Rudrax-Authd: false");

            if ($cache && !$cache_file) {
                header("X-Rudrax-Enabled: true");
                $this->responseCache = new RxCache ('responseCache');
                $this->headerCache = new RxCache ('headerCache');


                if (defined("RX_RESP_CACHE_TIME")) {
                    $this->cacheDuration = RX_RESP_CACHE_TIME;
                } else {
                    $this->cacheDuration = 900; // in seconds
                }
                // Client is told to cache these results for set duration
                header('Cache-Control: public,max-age=' . $this->cacheDuration . ',must-revalidate');
                header('Expires: ' . gmdate('D, d M Y H:i:s', ($_SERVER ['REQUEST_TIME'] + $this->cacheDuration)) . ' GMT');
                header('Last-modified: ' . gmdate('D, d M Y H:i:s', $_SERVER ['REQUEST_TIME']) . ' GMT');
                // Pragma header removed should the server happen to set it automatically
                // Pragma headers can make browser misbehave and still ask data from server
                header_remove('Pragma');
                $md5key = md5($_SERVER ["REQUEST_URI"]);

                $response = $this->responseCache->get($md5key, FALSE);
                if (!empty ($response)) {

                    $perform = false;
                    echo $response;
                    $headerstr = $this->headerCache->get($md5key);
                    $headers = explode(self::$HEADER_GLUE, $headerstr);
                    foreach ($headers as $header) {
                        header($header);
                    }
                    header("X-Rudrax-Cached: true");
                    if ($validate) {
                        header("X-Rudrax-Authd: true");
                    }
                    exit ();
                } else {
                    if ($validate) {
                        header("X-Rudrax-Authd: true");
                    }
                    // ob_start('ob_gzhandler');
                }
            }

            if ($perform) {
                $this->_interceptor_($info, $params);
            }

            if ($perform && $cache) {
                $response = ob_get_contents();
                if($cache_file === true){
                    FileUtil::build_write("/cache_file/".Webapp::$REQUEST_PATHNAME,$response);
                } else {
                    $this->responseCache->set($md5key, $response);
                    $this->headerCache->set($md5key, implode(self::$HEADER_GLUE, headers_list()));
                }
                // ob_end_flush();
                // echo $response;
            }
        }

        public function caching_headers($file, $timestamp)
        {
            $md5key = md5($timestamp . $file);
            $gmt_mtime = gmdate('r', $timestamp);
            header('ETag: "' . $md5key . '"');
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
                if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $md5key) {
                    header('HTTP/1.1 304 Not Modified');
                    exit();
                }
            }
            header('Last-Modified: ' . $gmt_mtime);
            header('Cache-Control: public');
            return $md5key;
        }

        public function _interceptor_($info, $params)
        {
            if (!isset($info ["type"])) {
                $info ["type"] = "data";
            }
            if (isset($info ["type"])) {
                $controller = $this;
                return call_user_func(
                    rx_function("rx_interceptor_" . $info ["type"]),
                    $this->user, $info, $params, function ($newParams) use ($controller, $info, $params) {
                        try {
                            $dontPrevent = true;
                            if(method_exists($controller,"_before_controller_")){
                                $dontPrevent = !!call_method_by_object($controller,
                                    "_before_controller_", $newParams, $info ["requestParams"]
                                );
                            }
                            $returned = null;
                            if($dontPrevent){
                                $returned =  call_method_by_object($controller,
                                    $info ["method"], $newParams, $info ["requestParams"]
                                );
                                if(method_exists($controller,"_post_controller_")){
                                    $returned = call_method_by_object($controller,
                                        "_post_controller_", $newParams, $info ["requestParams"]
                                    );
                                }
                                return $returned;
                            }
                        } catch (\Exception $e) {
                            if (function_exists("controller_exception_handler")) {
                                controller_exception_handler($e);
                            } else {
                                print_line("<div style='display:none'>**============**");
                                print_line("Controller Exception:" . $e->getMessage());
                                print_line("**--------------**");
                                print_line($e->getTraceAsString());
                                print_line("**============**</div>");
                            }
                        }
                        return "empty";
                    });

            }
        }

        public function header($key, $value)
        {
            return header($key . ': ' . $value);
        }

        public function status($code)
        {
            return http_response_code($code);
        }

        public function getInputJson(){
            return (array) json_decode(file_get_contents('php://input'), true);
        }
    }

}


