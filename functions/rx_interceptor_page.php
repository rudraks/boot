<?php

include_once (RUDRA . "/smarty/Smarty.class.php");
include_once (RUDRA . "/boot/model/Header.php");
function rx_interceptor_page($user, $info, $handlerName) {
	$user->validate ();
	include_once (RUDRA . "/boot/handler/AbstractHandler.php");
	
	$handlerInfo = ClassUtil::getHandler ( $handlerName );
	
	if ($handlerInfo != NULL) {
		global $temp;
		include_once $handlerInfo ["filePath"];
		$className = $handlerInfo ["className"];
		
		$tempClass = new ReflectionClass ( $className );
		if ($tempClass->isInstantiable ()) {
			$temp = $tempClass->newInstance ();
		}
		
		if ($temp != NULL) {
			if ($tempClass->hasMethod ( "invokeHandler" )) {
				$tpl = new Smarty ();
				call_user_func ( rx_function ( "rx_set_smarty_paths" ), ($tpl) );
				
				$tpl->debugging = RX_SMARTY_DEBUG;
				$header = new Header ( $tpl );
				$view = call_method_by_class ( $tempClass, $temp, 'invokeHandler', array (
						'tpl' => $tpl,
						'viewModel' => $tpl,
						'user' => $user,
						'header' => $header
				) , $handlerInfo ["requestParams"]);
				if (! isset ( $view )) {
					$view = $handlerName;
				}
				
				$tpl->assign ( 'user', $user );
				$tpl->assign ( 'header', $header );
				
				$tpl->assign ( 'CONTEXT_PATH', CONTEXT_PATH );
				$tpl->assign ( 'RESOURCE_PATH', RESOURCE_PATH );
				$tpl->assign ( 'METAS', $header->metas );
				$tpl->assign ( 'TITLE', $header->title );
				$view_path = $view . TEMP_EXT;
				
				if(!file_exists($view_path)){
					$tpl->setTemplateDir(RUDRA . "/boot/view");
					//$view_path = get_include_path () . RUDRA . "/core/view/".$view.TEMP_EXT;
					//$view_path = get_include_path () . RUDRA . "../view/".$view.TEMP_EXT;
				}
				
				$tpl->assign ( 'BODY_FILES', $view_path );
				$tpl->display ( RUDRA . "/boot/view/full.tpl" );
				Browser::log ( "header", $header->css, $header->scripts );
				Browser::printlogs ();
			}
		}
	}
}