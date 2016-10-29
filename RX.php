<?php
/**
 * Created by IntelliJ IDEA.
 * User: lalittanwar
 * Date: 25/10/16
 * Time: 3:54 PM
 */

namespace app;

class RX
{

    public static function handler($handle_name)
    {
        $CloudinaryClassInfo = \ClassUtil::getModel($handle_name);
        if ($CloudinaryClassInfo != NULL) {
            include_once $CloudinaryClassInfo["filePath"];
            $CloudinaryClassInstance = new $CloudinaryClassInfo["className"]();
            $CloudinaryClassInstance->populateParams();
            return $CloudinaryClassInstance;
        }
        return null;
    }
}