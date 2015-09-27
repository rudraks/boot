<?php 

define("BUILD_PATH", PROJECT_ROOT_DIR."build/");

define("LIB_PATH", PROJECT_ROOT_DIR."lib/");
define("RUDRA", LIB_PATH."rudrax/");
define("RUDRA_CORE", RUDRA."boot/");
define("RUDRA_MODEL", RUDRA_CORE."model/");
define("RUDRA_HANDLER", RUDRA_CORE."handler/");

define("APP_PATH", PROJECT_ROOT_DIR."app/");
define("VIEW_PATH", APP_PATH."view/");
define("CONFIG_PATH", APP_PATH."config/");

define("RESOURCE_PATH", PROJECT_ROOT_DIR."resources/");

function define_globals ($globals){

	set_include_path ( $globals ['PROJECT_ROOT_DIR'] );
	define ( "BASE_PATH", dirname ( __FILE__ ) );
    //return null;
	foreach ( $globals as $key=>$value ) {
        if(!defined($key)){
            define ( $key, $value );
        }
	}
}

?>