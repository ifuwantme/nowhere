<?php
Yaf_Loader::import('DB');
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午4:13
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{
    protected $config;
    protected $_db;

    public function _initSession($dispatcher) {
        Yaf_Session::getInstance()->start();
    }
    public function _initConfig() {
        $this->config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set("config", $this->config);
    }

    public function _initDb(Yaf_Dispatcher $dispatcher){
        $this->_db = new DB($this->config->mysql->read->toArray());
        Yaf_Registry::set('_db', $this->_db);
    }
}