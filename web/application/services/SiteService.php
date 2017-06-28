<?php
Yaf_Loader::import(APP_PATH.'/application/services/BaseService.php');
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午2:56
 */
class SiteService extends BaseService
{
    public function __construct()
    {

    }

    public function index(){
        return $this->getModel('SiteModel');

    }
}