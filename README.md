# boot
##install
```
  comoser install rudrax/boot
```

## Folder Structure
```
-app
  L controller  // Controller/URL Mapping for Project, name of file and class should match
  L model       //Models being Used in Porject
  L view        //Smart Templates
-config
  L project.properties //Project Properties
-build          // Build/Temporary Files created by Framwork, need write permissions
-src            // Folder for static files like javascript,html,css 
-lib            // composer library folder
-index.php
-composer.json  // set config->vendor-dir = lib

```
## index File [index.php]
```
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
error_reporting(E_ALL & ~E_DEPRECATED);


require("./lib/autoload.php");
require_once("./lib/rudrax/boot/RudraX.php");

RudraX::invoke(array(
    'RX_MODE_MAGIC' => TRUE,
    'RX_MODE_DEBUG' => FALSE,
    'PROJECT_ROOT_DIR' => "./"
));
```



## Sample Controller [app/controller/MyController.php]
```php
namespace app\controller {

    class MyController extends AbstractController
    {
        /**
         * @RequestMapping(url="login",method="GET",type="template")
         * @RequestParams(true)
         */
        public function login($model,$username,$password)
        {
            if($username == "user1" && $password == "xDddfdfd"){
              $this->user->setValid(TRUE);
              $this->user->role = "USER";
              $model->assign("username", $username);
              return "welcome"; // 'welcome' is path of smarty tempalte file in view folder
            } else {
              $this->user->setValid(FALSE);
               $model->assign("error", "Wrong credentials");
              return "login"; // 'login' is path of smarty tempalte file in view folder
            }
        }
        
        /**
         * @RequestMapping(url="myprofile",method="GET",type="template")
         * @RequestParams(true)
         */
        public function myprofile($model)
        {
            if($this->user->isValid()){
              $model->assign("username", $this->user->uname);
              return "user/myprofile"; // 'user/myprofile' is path of smarty tempalte file in view folder
            } else {
              $this->user->setValid(FALSE);
               $model->assign("error", "You need to login to view this page");
              return "login"; // 'login' is path of smarty tempalte file in view folder
            }
        }
        
        /**
         * @RequestMapping(url="info/school/{category}",method="GET",type="json")
         * @RequestParams(true)
         */
        public function schoolinfo($category)
        {
            if($this->user->isValid()){
              return array( "success" => true, "id" => 23,"name"=>"DAV Public School");
            } else {
              return array("success" => false,"error"=> "You need to login to view this info");
            }
        }

    }
}

```

## Controller Annotation Options
- @RequestMapping - URL info
  - **url** - url pattern to match
  - **method** - request method [GET/POST/PUT/DELETE] - used only if mentioned
  - **type** -  response type [template/json/data - data
  - **auth** - if url acccess requires basic auth [TRUE|FALSE] - FALSE
  - **cache** - if response is cacheable by server [TRUE|FALSE] - FALSE
  - **guestcache** - cacheable only if guest user (iser not valid) [TRUE|FALSE] - FALSE
- @RequestParams - if query params to be fetched and used in controller. [TRUE|FALSE] - FALSE  
- @Role - [user defined values] - used only if mentioned


