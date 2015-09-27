<?php

/**
 *  Whatever Data is returned by Hanlder, should be an array will be rendered as array on response
 *
 * @param AbstractUser $user
 * @param array $controllerInfo
 * @param String $controllerOutput
 */
function rx_interceptor_json($user, $controllerInfo, $params, $controllerExecute)
{
    try {
        echo json_encode($controllerExecute($params));
    } catch (Exception $e) {
        echo json_encode(array(
            "error" => $e
        ));
    }
}