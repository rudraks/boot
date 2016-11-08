namespace RudraX;

class RX
{

    public static function handler($handle_name)
    {
        $CloudinaryClassInfo = \ClassUtil::getHandler($handle_name);
        if ($CloudinaryClassInfo != NULL) {
            include_once $CloudinaryClassInfo["filePath"];
            $CloudinaryClassInstance = new $CloudinaryClassInfo["className"]();
            $CloudinaryClassInstance->populateParams();
            return $CloudinaryClassInstance;
        }
        return null;
    }
}
