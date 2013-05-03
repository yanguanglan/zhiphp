<?php

defined('THINK_PATH') or exit();

class check_inputBehavior extends Behavior {
    public function run(&$params){
        if(GROUP_NAME!='home') return;
        $_REQUEST=$this->filter($_REQUEST);
        $_GET=$this->filter($_GET);
        $_POST=$this->filter($_POST);
    }
    protected function filter($list){
        foreach($list as $key=>$val){
            $list[$key]=Input::safeHtml($val);
        }
        return $list;
    }
}