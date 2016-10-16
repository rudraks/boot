<?php

include_once(RUDRA . "/smarty/Smarty.class.php");
function rx_interceptor_template($user, $controllerInfo, $params, $controllerExecute)
{

    $tpl = new Smarty ();

    $params["model"] = $tpl;

    $view = $controllerExecute($params);

    call_user_func(rx_function("rx_set_smarty_paths"), ($tpl));

    $tpl->debugging = RX_SMARTY_DEBUG;

    $tpl->assign('user', $user);
    $tpl->assign('CONTEXT_PATH', CONTEXT_PATH);
    $tpl->assign('RESOURCE_PATH', RESOURCE_PATH);

    if (empty($view)) {
        echo "!!Empty Template!!";
        return;
    }
    if (isset ($tpl->repeatData)) {
        foreach ($tpl->repeatData as $key => $value) {
            $tpl->assign($value ['key'], $value ['value']);
            $tpl->display($view . Config::get('TEMP_EXT'));
        }
    } else {
        $tpl->display($view . Config::get('TEMP_EXT'));
    }

}