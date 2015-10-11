<?php

namespace app\controller {

    use \app\model\RxCache;

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
            $cache = $info ["cache"];
            $perform = true;
            $md5key = null;
            if ($cache) {
                header("X-Rudrax-Enabled: true");
                $this->responseCache = new RxCache ('responseCache');
                $this->headerCache = new RxCache ('headerCache');

                $this->cacheDuration = 300; // in seconds
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
                    exit ();
                } else {
                    // ob_start('ob_gzhandler');
                }
            }

            if ($perform) {
                $this->_interceptor_($info, $params);
            }

            if ($perform && $cache) {
                $response = ob_get_contents();
                $this->responseCache->set($md5key, $response);
                $this->headerCache->set($md5key, implode(self::$HEADER_GLUE, headers_list()));
                // ob_end_flush();
                // echo $response;
            }
        }

        public function _interceptor_($info, $params)
        {
            if(!isset($info ["type"])){
                $info ["type"] = "data";
            }
            if (isset($info ["type"])) {
                $controller = $this;
                return call_user_func(
                    rx_function("rx_interceptor_" . $info ["type"]),
                    $this->user, $info, $params, function ($newParams) use ($controller,$info,$params) {
                        try {
                            return call_method_by_object($controller,
                                $info ["method"], $newParams, $info ["requestParams"]
                            );
                        } catch(\Exception $e){
                            print_line("hey you",$e->getMessage());
                        }
                    });

            }
        }
    }

}


