<?php

include_once(RUDRA . "/smarty/Smarty.class.php");
include_once(RUDRA . "/boot/model/Header.php");
function rx_interceptor_page($user, $controllerInfo, $params, $controllerExecute)
{
    ?>
    <html>
    <?

    $tpl = new Smarty ();
    $params["model"] = $tpl;
    $header = new \app\model\Header ($tpl);
    call_user_func(rx_function("rx_set_smarty_paths"), ($tpl));
    $view = $controllerExecute();

    $tpl->debugging = RX_SMARTY_DEBUG;

    $tpl->assign('user', $user);
    $tpl->assign('header', $header);

    $tpl->assign('CONTEXT_PATH', CONTEXT_PATH);

    $tpl->assign('METAS', $header->metas);
    $tpl->assign('TITLE', $header->title);

    ?>
    <head></head>
<body>

    <?

    $tpl->display($view_path = $view . TEMP_EXT);

    ?></body><?

    Browser::log("header", $header->css, $header->scripts);
    Browser::printlogs();

    ?></html><?
}
?>

