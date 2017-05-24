<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.template_exists.php
 * Type:     modifier
 * Name:     template_exists
 * Purpose:  Test if a template exists
 * -------------------------------------------------------------
 */
function smarty_modifier_template_exists($file){
    if (empty($file)) return false;
    $oSmarty = \app\service\Smarty::getInstance()   ;
    $templates = $oSmarty->getTemplateDir();
    foreach($templates as $template){
        if(file_exists($template . $file)){
            return true;
        }
    }
    return false;
}