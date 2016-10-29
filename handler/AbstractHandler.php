<?php

/*
 * To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace app\handler {
    abstract class AbstractHandler
    {

        public function requestGet($key)
        {
            if (isset($_GET[$key])) {
                return $_GET[$key];
            }
            return "";
        }

        public function requestPost($key)
        {
            if (isset($_POST[$key])) {
                return $_POST[$key];
            }
            return FALSE;
        }

        public function populateParams()
        {
            foreach ($this as $key => $value) {
                if (isset($_REQUEST[$key])) {
                    $this->{$key} = $_REQUEST[$key];
                }
            }
        }
    }
}