<?php
Yaf_Loader::import(APP_PATH.'/application/services/BaseService_Imp.php');
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午3:01
 */
abstract class  BaseService implements BaseService_Imp
{
    protected $_service = null;

    public function getModel($m)
    {

        Yaf_Loader::import(APP_PATH.'/application/models/'.$m.'.php');
//        $this->_service = ReflectionClass::export($m);
        $class = new ReflectionClass($m);
        $this->_service = $class->newInstance();
        $this->_service->get();
    }

    public function queryFactory($db)
    {
        // TODO: Implement queryFactory() method.
        echo 'cba';
    }
}