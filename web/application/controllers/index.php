<?php
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/8
 * Time: 下午5:51
 */


class IndexController extends Yaf_Controller_Abstract{

    public function indexAction(){
        $this->getView()->assign("content", "Hello World");
    }
}