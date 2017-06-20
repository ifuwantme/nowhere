<?php
Yaf_Loader::import(APP_PATH.'/application/services/LineService.php');


/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/8
 * Time: 下午5:51
 */


class IndexController extends Yaf_Controller_Abstract{

    protected function init(){
        $this->lineService = new LineService();
    }
    public function indexAction(){

        $message = $this->lineService->index();
        $this->getView()->assign("content", $message);
        
    }
}