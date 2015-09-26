<?php
/**
 *  Whatever data is returned by handler will be echo'd as it is
 *
 * @param AbstractUser $user
 * @param Array $info
 * @param String $handlerName
 */
function rx_interceptor_data($user, $controllerInfo, $params, $controllerExecute)
{
    return $controllerExecute();
}