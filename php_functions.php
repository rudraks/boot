<?php

function is_remote($file_name) {
	return strpos ( $file_name, '://' ) > 0 ? 1 : 0;
}
function is_remote_file($file_name) {
	return is_remote ( $file_name ) && preg_match ( "#\.[a-zA-Z0-9]{1,4}$#", $file_name ) ? 1 : 0;
}
function replace_first($search, $replace = "", $subject = "") {
	if(empty($search)){
		return $subject;
	}
	$pos = strpos ( $subject, $search );
	if ($pos !== false) {
		$newstring = substr_replace ( $subject, $replace, $pos, strlen ( $search ) );
	}
	return $newstring;
}
function print_js_comment($str) {
	echo "/*  ";
	foreach (func_get_args() as $ar){
	 echo  "\n* ".$ar ;
	}
	echo " */";
}
function print_line($str) {
	echo "<br/>  " . $str;
}
function resolve_path($str) {
	$array = explode ( '/', $str );
	$domain = array_shift ( $array );
	$parents = array ();
	foreach ( $array as $dir ) {
		switch ($dir) {
			case '.' :
				// Don't need to do anything here
				break;
			case '..' :
				$popped = array_pop ( $parents );
				if(empty($popped)){
					//Its meaningful, cant afford to loose it
					$parents [] = $dir;
				} else if($popped == ".."){
					//Sorry, will have to put it back
					$parents [] = $popped;
					$parents [] = $dir;
				}
				break;
			case "" :
				//Some stupid guy didn't do his job :P
				break;
			default :
				$parents [] = $dir;
				break;
		}
	}
	return $domain . '/' . implode ( '/', $parents );
}


function file_put_contents_force(){
    $args = func_get_args();
    $path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $args[0]);
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    array_pop($parts);
    $directory =  '';
    foreach($parts as $part):
        $check_path = $directory.$part;
            if( is_dir($check_path.DIRECTORY_SEPARATOR) === FALSE) {
                mkdir($check_path, 0755);
            }
            $directory = $check_path.DIRECTORY_SEPARATOR;
    endforeach;     
    call_user_func_array('file_put_contents',$args);
}

function rx_function($callback) {
	include_once 'functions/' . $callback . ".php";
	return $callback;
}

// Param Utilities
function get_request_param($key, $skipEmpty = FALSE) {
	if (isset ( $_REQUEST [$key] ) && ! ($skipEmpty && empty ( $_REQUEST [$key] ))) {
		return $_REQUEST [$key];
	} else {
		return NULL;
	}
}
function get_argument_array($reflectionMethod, $argArray, $from_request = TRUE, $skipEmpty = FALSE) {
	$arr = array ();
    //var_dump($argArray);
	foreach ( $reflectionMethod->getParameters () as $key => $val ) {
        //print_line("0==".$val->getName ()."===".$val);
		if (isset ( $argArray [$val->getName ()] ) && ! ($skipEmpty && empty ( $argArray [$val->getName ()] ))) {
			$arr [$val->getName ()] = $argArray [$val->getName ()];
           // print_line("1==".($val->getName ())."===");
		} else if ($from_request && ! is_null ( get_request_param ( $val->getName (), $skipEmpty ) )) {
			$arr [$val->getName ()] = get_request_param ( $val->getName () );
            //print_line("2==".$val->getName ()."===".$val);
		} else if ($val->isDefaultValueAvailable ()) {
			$arr [$val->getName ()] = $val->getDefaultValue ();
            //print_line("3==".$val->getName ()."===".$val);
		} else {
			$arr [$val->getName ()] = NULL;
		}
	}
	return $arr;
}
function call_method_by_class(ReflectionClass $reflectionClass, $object, $methodName, $argArray, $from_request = NULL) {
	$reflectionMethod = $reflectionClass->getMethod ( $methodName );
	return call_user_func_array ( array (
			$object,
			$methodName 
	), get_argument_array ( $reflectionMethod, $argArray, $from_request ) );
}
function call_method_by_object($object, $methodName, $argArray, $from_request = NULL) {
	$reflectionClass = new ReflectionClass ( get_class ( $object ) );
	$reflectionMethod = $reflectionClass->getMethod ( $methodName );

	return call_user_func_array ( array (
			$object,
			$methodName 
	), get_argument_array ( $reflectionMethod, $argArray, $from_request ) );
}
function removecookie($key, $context = "/") {
	if (isset ( $_COOKIE [$key] )) {
		unset ( $_COOKIE [$key] );
		setcookie ( $key, null, - 1, $context );
		return true;
	} else {
		return false;
	}
}


function str_starts_with($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}

// ERROR TRACE BACK FUNCTION
function process_error_backtrace($errno, $errstr, $errfile, $errline, $errcontext) {
	if (! (error_reporting () & $errno))
		return;
	switch ($errno) {
		case E_WARNING :
		case E_USER_WARNING :
		case E_STRICT :
		case E_NOTICE :
		case E_USER_NOTICE :
			$type = 'warning';
			$fatal = false;
			break;
		default :
			$type = 'fatal error';
			$fatal = true;
			break;
	}
	$trace = array_reverse ( debug_backtrace () );
	array_pop ( $trace );
	if (php_sapi_name () == 'cli') {
		echo 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
		foreach ( $trace as $item )
			echo '  ' . (isset ( $item ['file'] ) ? $item ['file'] : '<unknown file>') . ' ' . (isset ( $item ['line'] ) ? $item ['line'] : '<unknown line>') . ' calling ' . $item ['function'] . '()' . "\n";
	} else {
		echo '<p class="error_backtrace">' . "\n";
		echo '  Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
		echo '  <ol>' . "\n";
		foreach ( $trace as $item )
			echo '    <li>' . (isset ( $item ['file'] ) ? $item ['file'] : '<unknown file>') . ' ' . (isset ( $item ['line'] ) ? $item ['line'] : '<unknown line>') . ' calling ' . $item ['function'] . '()</li>' . "\n";
		echo '  </ol>' . "\n";
		echo '</p>' . "\n";
	}
	if (ini_get ( 'log_errors' )) {
		$items = array ();
		foreach ( $trace as $item )
			$items [] = (isset ( $item ['file'] ) ? $item ['file'] : '<unknown file>') . ' ' . (isset ( $item ['line'] ) ? $item ['line'] : '<unknown line>') . ' calling ' . $item ['function'] . '()';
		$message = 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ': ' . join ( ' | ', $items );
		error_log ( $message );
	}
	if ($fatal)
		exit ( 1 );
}



set_error_handler ( 'process_error_backtrace' );
//////////