<?php

/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午4:13
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{
    protected $config;
    public function _initSession($dispatcher) {
        Yaf_Session::getInstance()->start();
    }
    public function _initConfig() {
        $this->config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set("config", $this->config);
    }
}