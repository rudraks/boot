<?php

require_once RUDRA."annotations/Annotations.php";
include_once(RUDRA."boot/handler/AbstractHandler.php");
include_once(RUDRA."boot/controller/AbstractController.php");
include_once(RUDRA."boot/ClassUtil.php");

function rx_scan_classes(){


	$annotations = new Alchemy\Component\Annotations\Annotations();

	if(is_dir(LIB_PATH)){
		rx_scan_dir($annotations,LIB_PATH);
	} else {
		echo "Error:Library directory not found on project root.";
	}
	
	if(is_dir(APP_PATH)){
		rx_scan_dir($annotations,APP_PATH);
	}

	ClassUtil::save();
}

function rx_scan_dir ($annotations,$dir){
	
	$allController = ClassUtil::getControllerArray();
	
	$dir_iterator = new RecursiveDirectoryIterator($dir);
	$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
	// could use CHILD_FIRST if you so wish
	
	foreach ($iterator as $filename=>$file) {
		if ($file->isFile()) {
			if(fnmatch("*/handler/*.php",$file->getPathname()) || fnmatch("*\\\handler\\\*.php",$file->getPathname())){
				require_once $file->getPathname();
				$className = str_replace(".php", "", $file->getFilename());
					
				$scan  = true;
				if( ClassUtil::getMTime($className)>=$file->getMTime()){
					$scan = false;
				}

				if($scan && class_exists($className)){
					$result = $annotations->getClassAnnotations($className);
					if(isset($result["Handler"]) && isset($result["Handler"][0]) && !empty($result["Handler"][0])){
						ClassUtil::setHandler($result["Handler"][0], array(
						"className" => $className,
						"filePath" => $file->getPathname(),
						"mtime" => $file->getMTime(),
						"requestParams" => isset($result["RequestParams"])
						));
						ClassUtil::setMTime($className,$file->getMTime());
					}
				}
			} else if(fnmatch("*/controller/*.php",$file->getPathname()) || fnmatch("*\\\controller\\\*.php",$file->getPathname())){
	
				require_once $file->getPathname();
				$className =  "app\\controller\\".str_replace(".php", "", $file->getFilename());
					
				$scan  = true;
				if(ClassUtil::getMTime($className)>=$file->getMTime()){
					$scan = false;
				}

				if($scan && class_exists($className)){
					$methods = get_class_methods($className);
					foreach ($methods as $method){
						$result = $annotations->getMethodAnnotations($className,$method);
						if(isset($result["RequestMapping"])
						&&	isset($result["RequestMapping"][0])
						&&  isset($result["RequestMapping"][0]["url"])){
							$allController[] = array(
									"className" => $className,
									"method" => $method,
									"filePath" => $file->getPathname(),
									"mtime" => $file->getMTime(),
									"mappingUrl" => $result["RequestMapping"][0]["url"],
									"requestParams" => isset($result["RequestParams"]),
                                    "roles" => isset($result["Role"]) ? $result["Role"] : FALSE,
                                    "auth" => (isset($result["RequestMapping"][0]["auth"]) ? $result["RequestMapping"][0]["auth"] : FALSE),
									"cache" => (isset($result["RequestMapping"][0]["cache"]) ? $result["RequestMapping"][0]["cache"] : FALSE),
									"guestcache" => (isset($result["RequestMapping"][0]["guestcache"]) ? $result["RequestMapping"][0]["guestcache"] : FALSE),
									"type" => (isset($result["RequestMapping"][0]["type"]) ? $result["RequestMapping"][0]["type"] : NULL),
									"requestMethod" => (isset($result["RequestMapping"][0]["method"]) ? strtoupper($result["RequestMapping"][0]["method"]) : NULL),
							);
						}
					}
					ClassUtil::setMTime($className,$file->getMTime());
				}
			} else if(fnmatch("*/model/*.php",$file->getPathname()) || fnmatch("*\\\model\\\*.php",$file->getPathname())){
				require_once $file->getPathname();
				$className = "app\\model\\".str_replace(".php", "", $file->getFilename());
	
				$scan  = true;
				if( ClassUtil::getMTime($className)>=$file->getMTime()){
					$scan = false;
				}
	
				if($scan && class_exists($className)){
					$result = $annotations->getClassAnnotations($className);
					if(isset($result["Model"]) && isset($result["Model"][0]) && !empty($result["Model"][0])){
						ClassUtil::setModel($result["Model"][0], array(
						"className" => $className,
						"filePath" => $file->getPathname(),
						"mtime" => $file->getMTime(),
						"type" => $result["Model"][0]
						));
						ClassUtil::setMTime($className,$file->getMTime());
					}
				}
			}
		}
	}
	
	ClassUtil::setControllerArray($allController);
}